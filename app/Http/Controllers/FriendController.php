<?php

namespace App\Http\Controllers;

use App\Friend;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    public function addOrDelete(Request $request)
    {
        if ($request->ajax()) {
            if ($request->doAction != '') {
                $isFriend = Friend::where('user', Auth::user()->id)->where('friend', $request->userId)->first();
                if ($isFriend) {
                    switch ($isFriend->status) {
                        case -1:
                            if ($request->doAction == 'add') {
                                $isFriend->status = 1;
                                $isFriend->save();
                                $friend = Friend::where('user', $request->userId)
                                    ->where('friend', Auth::user()->id)
                                    ->first();
                                $friend->status = 1;
                                $friend->save();
                                $result = [
                                    'canDelete' => true
                                ];
                            } else {
                                $isFriend->delete();
                                $friend = Friend::where('user', $request->userId)
                                    ->where('friend', Auth::user()->id)
                                    ->get();
                                $friend->delete();
                                $result = [
                                    'canAdd' => true
                                ];
                            }
                            break;
                        case 1:
                            $isFriend->delete();
                            $friend = Friend::where('user', $request->userId)
                                ->where('friend', Auth::user()->id)
                                ->first();
                            $friend->delete();
                            $result = [
                                'canAdd' => true
                            ];
                            break;
                    }
                    echo json_encode($result);
                } else {
                    $userFriend = new Friend([
                        'user' => Auth::user()->id,
                        'friend' => $request->userId,
                        'status' => 0
                    ]);
                    $newFriend = new Friend([
                        'user' => $request->userId,
                        'friend' => Auth::user()->id,
                        'status' => -1
                    ]);
                    $userFriend->save();
                    $newFriend->save();
                    $result = [
                        'awaiting' => true
                    ];
                    echo json_encode($result);
                }
            } else {
                $isFriend = Friend::where('user', Auth::user()->id)->where('friend', $request->userId)->first();
                if ($isFriend) {
                    switch ($isFriend->status) {
                        case -1:
                            $result = [
                                'answer' => true
                            ];
                            break;
                        case 0:
                            $result = [
                                'awaiting' => true
                            ];
                            break;
                        case 1:
                            $result = [
                                'canDelete' => true
                            ];
                            break;
                    }
                    echo json_encode($result);
                } else {
                    $result = [
                        'canAdd' => true
                    ];
                    echo json_encode($result);
                }
            }
        }
    }

    public function showFriends(Request $request)
    {
        if ($request->ajax()) {
            $friendsID = [];
            $friends = Friend::where('user', $request->userId)
                ->where('status', 1)
                ->get();
            foreach ($friends as $friend) {
                array_push($friendsID, $friend->friend);
            }
            $friendList = User::whereIn('id', $friendsID)->get();
            foreach ($friendList as $friend){
                $friend->link = route('profile.show', $friend->name);
            }
            echo json_encode($friendList);
        }
    }

    public function awaitingAnswer(Request $request){
        if($request->ajax()){
            if($request->doAction != ''){
                if($request->doAction == 'add'){
                    $firstUser = Friend::where('user', Auth::user()->id)
                        ->where('friend', $request->id)
                        ->first();
                    $secondUser = Friend::where('user', $request->id)
                        ->where('friend', Auth::user()->id)
                        ->first();
                    $firstUser->status = 1;
                    $firstUser->save();
                    $secondUser->status = 1;
                    $secondUser->save();
                }
                else{
                    $firstUser = Friend::where('user', Auth::user()->id)
                        ->where('friend', $request->id)
                        ->first();
                    $secondUser = Friend::where('user', $request->id)
                        ->where('friend', Auth::user()->id)
                        ->first();
                    $firstUser->delete();
                    $secondUser->delete();
                }
            }
            $friendsID = [];
            $friends = Friend::where('user', Auth::user()->id)
                ->where('status', -1)
                ->get();
            foreach ($friends as $friend) {
                array_push($friendsID, $friend->friend);
            }
            $awaiting = User::whereIn('id', $friendsID)->get();
            foreach ($awaiting as $friend){
                $friend->link = route('profile.show', $friend->name);
            }
            echo json_encode($awaiting);
        }
    }
}
