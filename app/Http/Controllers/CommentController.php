<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function addComment(Request $request){
        If($request->ajax()){
            if(Auth::check()){
                $user = Auth::user();
                $comment = new Comment(array(
                    'content' => $request->comment,
                    'post_id' => $request->postId));
                $user->comments()->save($comment);
                $result = 'success';
                echo json_encode($result);
            }
            else {
                $result = 'error';
                echo json_encode($result);
            }
        }
    }

    public function showComments(Request $request){
        $comments = Post::find($request->postId)->comments;
        foreach ($comments as $comment){
            $comment->author = $comment->user->name;
            $comment->authorProfile = route('profile.show', $comment->user->name);
        }
        echo json_encode($comments);
    }
}
