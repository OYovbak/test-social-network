<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('posts.postList');
//});
Route::get('/', 'PostController@postList')->name('postList');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/profile/{name}', 'ProfileController@profileShow')->name('profile.show');
Route::post('/profile/friend', 'FriendController@addOrDelete')->name('profile.friend');
Route::post('/profile/showFriends', 'FriendController@showFriends')->name('profile.showFriends');
Route::post('profile/awaitingAnswer', 'FriendController@awaitingAnswer')->name('profile.awaitingAnswer');

Route::get('/postList/myPosts', 'PostController@myPosts')->name('myPosts');
Route::post('/postList/myPosts/show', 'PostController@showPosts')->name('posts.show');
Route::post('/postList/myPosts/create', 'PostController@createPost')->name('post.createPost');
Route::post('/postList/myPosts/delete', 'PostController@deletePost')->name('post.delete');

Route::get('/postList/{title}', 'PostController@postPage')->name('postPage');
Route::post('/postList/favorite', 'PostController@postFavorite')->name('post.favorite');

Route::post('/comments/add', 'CommentController@addComment')->name('comment.add');
Route::post('/comments/show', 'CommentController@showComments')->name('comment.show');
