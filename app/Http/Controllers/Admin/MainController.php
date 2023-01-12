<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\BaseAdminController;

class MainController extends BaseAdminController
{
    public function index(){
        return view('v1/admin/main');
    }

}
