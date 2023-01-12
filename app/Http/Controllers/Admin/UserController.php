<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseAdminController
{
    public function list(Request $request)
    {
        $usersQuery = User::query();
        $filters = [
            'email' => (string)$request->get('email', ''),
        ];
        if ($filters['email']) {
            $usersQuery->where('email', $filters['email']);
        }

        $usersListRequest = $usersQuery->get()->take(10);
        $usersArray = array();
        foreach ($usersListRequest as $users) {
            $usersArray[] = [
                'id' => $users->id,
                'name' => $users->name,
                'email' => $users->email,
            ];
        }

        return response()->json([
            'usersList' => $usersArray,
        ]);
    }

    public function create(Request $request)
    {
        $name = (string)$request->input('name');
        $email = (string)$request->input('email');
        $password = (string)$request->input('password');

        if ($this->validateUserData($name, $email, $password)) {

            $newUser = new User();
            $newUser->name = $name;
            $newUser->email = $email;
            $newUser->password = $password;
            $newUser->save();
            $newUserId = $newUser->id;
            $response = Response::HTTP_OK;
            $status = 'ok';
        } else {

            $response = Response::HTTP_BAD_REQUEST;
            $newUserId = 0;
            $status = 'cant create';
        }

        return response()->json(['status' => $status, 'userId' => $newUserId])->setStatusCode($response);
    }

    public function get($userId)
    {
        /**
         * @var User $foundUser
         */
        $foundUser = User::find($userId);
        $userData = [];
        if ($foundUser) {
            $response = Response::HTTP_OK;
            $status = 'ok';
            $userData['name'] = $foundUser->getName();
            $userData['email'] = $foundUser->getEmail();
        } else {
            $response = Response::HTTP_NOT_FOUND;
            $status = 'User not found';
        }
        return response()->json([
            'status' => $status,
            'user' => $userData
        ])->setStatusCode($response);

    }

    public function update(Request $request)
    {

        $userId = $request->route()->parameter('userId');
        $name = (string)$request->input('name');
        $email = (string)$request->input('email');
        $password = (string)$request->input('password');

        if ($this->validateUserData($name, $email, $password)) {

            $user = User::find($userId);
            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            $user->save();
            $response = Response::HTTP_OK;
            $status = 'ok';
        } else {

            $response = Response::HTTP_NOT_FOUND;
            $status = 'cant update';
        }

        return response()->json(['status' => $status])->setStatusCode($response);

    }

    public function delete(Request $request)
    {
        $userId = $request->route()->parameter('userId');
        $foundUser = User::find($userId);
        if ($foundUser) {
            $foundUser->delete();
            $response = Response::HTTP_OK;
            $status = 'ok';
        } else {
            $response = Response::HTTP_NOT_FOUND;
            $status = 'User not found';
        }
        return response()->json(['status' => $status])->setStatusCode($response);
    }

    private function validateUserData(string $name, string $email, string $password): bool
    {
        if (mb_strlen($name) == 0) {
            return false;
        }
        if (!mb_strripos($email, '@')) {
            return false;
        }
        if (mb_strlen($password) == 0) {
            return false;
        }
        return true;
    }
}
