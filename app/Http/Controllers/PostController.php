<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    public function postList(){
        return view('posts.postList');
    }

    public function postPage($title){
        $post = Post::where('title', '=', $title)->first();
        return view('posts.postpage', ['post'=>$post]);
    }

    public function showPosts(Request $request){
        if($request->id){
            $user = User::find($request->id);
            $posts = $user->posts;
        }
        else{
            $posts = Post::all();
        }
        foreach ($posts as $post){
            $post->link = route('postPage', $post->title);
            $post->author = $post->user->name;
            $post->authorProfile = route('profile.show', $post->user->name);
        }
        echo json_encode($posts);
    }

    public function myPosts(){
        return view('users.myPosts');
    }

    public function createPost(Request $request){
        $title = $request->title;
        $content = $request->text;
        $user = User::find(Auth::user()->id);
        $post = new Post(array(
            'title' => $title,
            'content' => $content,
        ));
        $user->posts()->save($post);
        $data = [
            'success' => 'post saved',
        ];
        echo json_encode($data);
    }

    public function deletePost(Request $request){
        $post = Post::find($request->idToDelete);
        $comments = $post->comments->all();
        foreach ($comments as $comment){
            $comment->delete();
        }
        $post->delete();
        $result = [
            'success' => 'Post delete',
        ];
        echo json_encode($result);
    }

    public function postFavorite(Request $request){
        if($request->ajax()){
            if($request->doAction != ''){
                $post = Post::find($request->postId);
                if($post->inFavorite()->get()->contains('id', Auth::user()->id)) {
                    $post->inFavorite()->detach(Auth::user()->id);
                    $data = [
                        'canDelete' => '0'
                    ];
                }
                else{
                    $post->inFavorite()->attach(Auth::user()->id);
                    $data = [
                        'canDelete' => '1'
                    ];
                }
                echo json_encode($data);
            }
            else{
                $post = Post::find($request->postId);
                $data = [
                    'canDelete' => '0'
                ];
                if($post->inFavorite()->get()->contains('id', Auth::user()->id)) {
                    $data = [
                        'canDelete' => '1'
                    ]; }
                echo json_encode($data);
            }
        }
    }
}
