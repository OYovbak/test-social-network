<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profileShow($name){
        $user = User::where('name', '=', $name)->first();
        return view('users.profile', ['user'=>$user]);
    }

    public function userCheck(Request $request){
        if($request->ajax()){
            if(Auth::check()){
                if($request->userId == Auth::user()->id){
                    echo json_encode($result = 1);
                }
                else echo json_encode($result = 0);
            }
            else{
                echo json_encode($result = 1);
            }
        }
    }
}
