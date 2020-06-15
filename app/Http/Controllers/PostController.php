<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        elseif($request->search){
            $posts = Post::where('title', 'like', '%'.$request->search.'%')
                ->get();
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
        if(Auth::check()){
        return view('users.myPosts');
        }
        else return redirect(route('postList'));

    }

    public function createPost(Request $request)
    {
        if ($request->ajax()) {
            $rules = [
                'title' => 'required|unique:posts',
                'text' => 'required',
                'url_img' => 'nullable|sometimes|image',
            ];
            $error = Validator::make($request->all(), $rules);
            if($error->fails()){
                $data = [
                    'errors' => $error->errors()->all()
                ];
                echo json_encode($data);
            }
            else{
                if($request->url_img){
                $image = $request->file('url_img')->store('images', 'public');
                $url_img = Storage::url($image);
                }
                else {
                    $url_img = null;
                }
                $title = $request->title;
                $content = $request->text;
                $user = User::find(Auth::user()->id);
                $post = new Post([
                    'title' => $title,
                    'content' => $content,
                    'url_img' => $url_img
                ]);
                $user->posts()->save($post);
                $data = [
                    'success' => 'post saved',
                ];
                echo json_encode($data);
            }
        }
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

    public function postInfo(Request $request){
        $postInfo = Post::where('id', $request->idToUpdate)->first();
        echo json_encode($postInfo);
    }

    public function updatePost(Request $request){
        if($request->ajax()){
            $rules = [
                'title' => 'nullable|unique:posts',
                'text' => 'nullable',
                'url_img' => 'nullable|sometimes|image',
            ];
            $error = Validator::make($request->all(), $rules);
            if($error->fails()){
                $data = [
                    'errors' => $error->errors()->all()
                ];
                echo json_encode($data);
            }
            else {
                $post = Post::find($request->id);
                if($request->title){
                    $post->title = $request->title;
                }
                if($request->text != $post->content){
                    $post->text = $request->content;
                }
                $old_img = '';
                if($request->url_img){
                    $image = $request->file('url_img')->store('images', 'public');
                    $url_img = Storage::url($image);
                    if($post->url_img != null){
                        $old_img = $post->url_img;
                    }
                    $post->url_img = $url_img;
                }
                $post->save();
                $data = [
                    'success' => 'Info changed',
                ];
                if($old_img != ''){
                    unlink(public_path($old_img));
                }
                echo json_encode($data);
            }
        }
    }
}
