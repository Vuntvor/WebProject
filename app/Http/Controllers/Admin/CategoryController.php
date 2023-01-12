<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class CategoryController extends BaseAdminController
{
    public function index(Request $request)
    {
        $categoryListRequest = Category::query()->get();
        if ($categoryListRequest->isEmpty()) {
            return response()->json(['empty data'])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

//        $parentsCategory = [];
//        foreach ($categoryListRequest as $category) {
//            $parentsCategory[$category->parent_category_id][$category->id] = $category;
//        }
//        $treeElem = $parentsCategory[0];
//        $this->generateElemTree($treeElem, $parentsCategory);

        return response()->json([
            'categoryList' => $request->input('skip_formatting')
                ? $categoryListRequest
                : $this->buildCategoriesTreeJsonResult($categoryListRequest),
        ]);

    }

    public function create(Request $request)
    {
        $categoryName = (string)$request->input('category_name');
        $parentCategoryId = (string)$request->input('parent_category_id');
        $categoryStatus = (boolean)$request->input('category_status');
        $categoryDescription = (string)$request->input('category_description');
        $categoryAlias = (string)$request->input('alias');

        if ($this->validateCategoryData($categoryName, $categoryDescription)) {

            $newCategory = new Category();
            $newCategory->name = $categoryName;
            $newCategory->parent_category_id = $parentCategoryId;
            $newCategory->status = $categoryStatus;
            $newCategory->description = $categoryDescription;
            $newCategory->alias = $categoryAlias;
            $newCategory->save();

            $this->uploadCategoryImage($newCategory, $request);

            $newCategoryId = $newCategory->id;
            $response = Response::HTTP_OK;
            $status = 'ok';
        } else {
            $response = Response::HTTP_BAD_REQUEST;
            $newCategoryId = 0;
            $status = 'cant create';
        }
        return response()->json(['status' => $status, 'categoryId' => $newCategoryId])->setStatusCode($response);
    }

    public function oneCategory($categoryId)
    {
        $foundCategory = Category::find($categoryId);
        $categoryData = [];
        if ($foundCategory) {
            $response = Response::HTTP_OK;
            $status = 'ok';
            $categoryData['category_name'] = $foundCategory->category_name;
            $categoryData['parent_category_id'] = $foundCategory->parent_category_id;
            $categoryData['category_status'] = $foundCategory->category_status;
            $categoryData['image_url'] = $foundCategory->image_url;
            $categoryData['category_description'] = $foundCategory->category_description;
            $categoryData['alias'] = $foundCategory->alias;
        } else {
            $response = Response::HTTP_NOT_FOUND;
            $status = 'Category not found';
        }
        return response()->json([
            'status' => $status,
            'category' => $categoryData
        ])->setStatusCode($response);

    }

    public function update(Request $request)
    {
        $categoryId = $request->route()->parameter('id');

        $foundCategory = Category::find($categoryId);

        if ($foundCategory) {

            $categoryName = (string)$request->input('category_name');
            $categoryParent = (string)$request->input('parent_category_id');
            $categoryStatus = (boolean)$request->input('category_status');
            $categoryDescription = (string)$request->input('category_description');
            $categoryAlias = (string)$request->input('alias');

            if ($this->validateCategoryData($categoryName, $categoryDescription)) {

                $foundCategory->name = $categoryName;
                $foundCategory->parent_category_id = $categoryParent;
                $foundCategory->status = $categoryStatus;
                $this->uploadCategoryImage($foundCategory, $request);
                $foundCategory->description = $categoryDescription;
                $foundCategory->alias = $categoryAlias;
                $foundCategory->save();

                $response = Response::HTTP_OK;
                $status = 'ok';

            } else {

                $response = Response::HTTP_NOT_FOUND;
                $status = 'Cant update. Name or description is empty.';
            }
        } else {

            $response = Response::HTTP_NOT_FOUND;
            $status = 'Cant update. Category not found.';
        }


        return response()->json(['status' => $status])->setStatusCode($response);
    }

    public function delete(Request $request)
    {
        $categoryId = $request->route()->parameter('id');
        $foundCategory = Category::find($categoryId);
        if ($foundCategory) {
            $foundCategory->delete();

            $response = Response::HTTP_OK;
            $status = 'ok';
        } else {
            $response = Response::HTTP_NOT_FOUND;
            $status = 'Category not found';
        }
        return response()->json(['status' => $status])->setStatusCode($response);
    }

    public function status(Request $request)
    {
        $categoryId = $request->route()->parameter('id');
        $foundCategory = Category::find($categoryId);

        if ($foundCategory->isEmpty()) {
            $response = Response::HTTP_NOT_FOUND;
            $status = 'Category not found';
        } else {
            $categoryStatus = (boolean)$request->input('status');
            $foundCategory->status = $categoryStatus;
            $foundCategory->save();
            $response = Response::HTTP_OK;
            if ($categoryStatus == 0) {
                $status = 'category off';
            } else {
                $status = 'category on';
            }
        }
        return response()->json(['status' => $status])->setStatusCode($response);
    }

    private function validateCategoryData($categoryName, $categoryDescription)
    {
        if (mb_strlen($categoryName) == 0) {
            return false;
        }
        if (mb_strlen($categoryDescription) == 0) {
            return false;
        }
        return true;
    }

    private function generateElemTree($treeElem, $parentsCategory)
    {
        foreach ($treeElem as $key => $Elem) {
            if (!isset($Elem->children)) {
                $Elem->children = [];
            }
            if (array_key_exists($key, $parentsCategory)) {
                $Elem->children = $parentsCategory[$key];
                $this->generateElemTree($Elem->children, $parentsCategory);
            }
        }
    }

    protected function uploadCategoryImage(Category $newCategory, Request $request)
    {
        if (!$request->file('image_url')) {
            return;
        }

        $imageName = $request->file('image_url')->storeAs('category_image', $newCategory->id . '.jpg');
        $newCategory->image_url = $imageName;
        $newCategory->save();
    }

    protected function buildCategoriesTreeJsonResult(Collection $categories): array
    {
        $parentCategoriesIds = [];
        foreach ($categories as $category) {
            /** @var Category $category */
            $parentCategoriesIds[$category->parent_category_id][] = $category->id;
        }

        $result = [];
        foreach ($parentCategoriesIds[0] as $categoryId) {
            $category = $categories->filter(fn($c) => $categoryId == $c->id)->first();
            $result[] = $this->buildCategoryTreeElem($category, $categories, $parentCategoriesIds);
        }

        return $result;
    }

    protected function buildCategoryTreeElem(Category $category, Collection $allCategories, array $parentCategoriesIds): array
    {
        $treeElemData = [
            'id' => $category->id,
            'name' => $category->name,
            'alias' => $category->alias,
            'status' => $category->status,
            'image_url' => $category->image_url,
        ];
        if (array_key_exists($category->id, $parentCategoriesIds)) {
            foreach ($parentCategoriesIds[$category->id] as $categoryId) {
                $childrenCategory = $allCategories->filter(fn($c) => $categoryId == $c->id)->first();
                $treeElemData['children'][] = $this->buildCategoryTreeElem($childrenCategory, $allCategories, $parentCategoriesIds);
            }
        }

        return $treeElemData;
    }
}
