<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Class BaseAdminController extends Controller
{
    public function getAuthUser(): User
    {
        return Auth::user();
    }
}
