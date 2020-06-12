<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class ProfileController extends Controller
{
    public function profileShow($name){
        $user = User::where('name', '=', $name)->first();
        return view('users.profile', ['user'=>$user]);
    }
}
