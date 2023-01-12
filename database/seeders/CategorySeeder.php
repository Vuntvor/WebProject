<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{

    public function run(): void
    {
        foreach ($this->data() as $categoryData) {
            $this->createCategory($categoryData);
        }
    }

    protected function createCategory(array $categoryData, Category $parentCategory = null)
    {
        $category = Category::factory()
            ->create([
                'parent_category_id' => $parentCategory?->id,
                'name' => $categoryData['name'],
                'alias' => Str::slug($categoryData['name'])
            ]);

        $categoryChildren = $categoryData['children'] ?? [];
        foreach ($categoryChildren as $childCategoryData) {
            $this->createCategory($childCategoryData, $category);
        }
    }

    protected function data(): array
    {
        return [
            [
                'name' => 'Зоотовары',
                'children' => [
                    [
                        'name' => 'Коты',
                        'children' => [
                            [
                                'name' => 'Корм',
                                'children' => [
                                    ['name' => 'Влажный корм'],
                                    ['name' => 'Сухой корм'],
                                    ['name' => 'Заменители молока'],
                                ]
                            ],
                            [
                                'name' => 'Наполнители туалетов',
                                'children' => [
                                    ['name' => 'Силикагелевый'],
                                    ['name' => 'Бентонитовый'],
                                    ['name' => 'Древесный'],
                                    ['name' => 'Соевый'],
                                    ['name' => 'Минеральный'],
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'Собаки',
                        'children' => [
                            [
                                'name' => 'Одежда',
                                'children' => [
                                    ['name' => 'Дождевики'],
                                    ['name' => 'Жилеты'],
                                    ['name' => 'Комбинезоны'],
                                    ['name' => 'Костюмы'],
                                    ['name' => 'Куртки'],
                                    ['name' => 'Майки'],
                                    ['name' => 'Футболки'],
                                    ['name' => 'Обувь'],
                                    ['name' => 'Платья'],
                                    ['name' => 'Свитера'],
                                    ['name' => 'Спортивные костюмы'],
                                    ['name' => 'Толстовки'],
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
