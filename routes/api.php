<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MainController as AdminMainController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/v1')->group(function () {
    Route::prefix('/rt-admin')->group(function () {

//        Route::post('/auth/register/', [AuthController::class, 'register'])->name('register');
//
        Route::post('/auth/login/', [AuthController::class, 'login'])->name('login');

        Route::middleware('auth:sanctum')->group(function () {

            Route::get('/auth/test/', [AuthController::class, 'test'])->name('log.test');

            Route::get('/users/list/', [UserController::class, 'list'])->name('users.list');

            Route::post('/users/create/', [UserController::class, 'create'])->name('users.create');

            Route::get('/users/get/{userId}', [UserController::class, 'get'])->name('users.get');

            Route::post('/users/update/{userId}', [UserController::class, 'update'])
                ->where('userId', '[0-9]+')
                ->name('users.update');

            Route::post('/users/delete/{userId}', [UserController::class, 'delete'])
                ->where('userId', '[0-9]+')
                ->name('users.delete');

            Route::prefix('/category')->group(function () {

                Route::post('/', [CategoryController::class, 'create'])->name('category.create');

                Route::get('/', [CategoryController::class, 'index'])->name('category.list');

                Route::get('/{id}', [CategoryController::class, 'oneCategory'])
                    ->where('id', '[0-9]+')
                    ->name('category.one');

                Route::put('/{id}', [CategoryController::class, 'update'])
                    ->where('id', '[0-9]+')
                    ->name('category.update');

                Route::delete('/{id}', [CategoryController::class, 'delete'])
                    ->where('id', '[0-9]+')
                    ->name('category.delete');

                Route::post('/{id}/status', [CategoryController::class, 'status'])
                    ->where('id', '[0-9]+')
                    ->name('category.status');

            });
        });
    });
});
