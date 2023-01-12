<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;


class AuthController extends BaseAdminController
{
    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($name) || empty($email) || empty($password)) {
            echo 'Ошибка, заполните: name, email, password';
        } else {
            User::create(["name" => $name, "email" => $email, "password" => Hash::make($password)]);
        }

    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $credentials = ["email" => $email, "password" => $password];
        if (Auth::attempt($credentials)) {
            $user = $this->getAuthUser();

            return response([
                'user' => $user->getName(),
                'token' => $this->getAuthUser()->createAuthToken()
            ]);

        } else {
            return response([], 422);
        }

    }
    public function test(Request $request)
    {
        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA',
        ]);
    }

}
