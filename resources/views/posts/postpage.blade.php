<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item nav-link navbar-text"><a class="menu_link" href="{{route('postList')}}">Post List</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile.show', \Illuminate\Support\Facades\Auth::user()->name) }}">My Profile</a>
                                <a class="dropdown-item" href="{{ route('myPosts') }}">My Posts</a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{$post->title}}</div>


                        {{ csrf_field() }}
                        <div class="card-body">
                            <div> Author: <a href="{{route('profile.show', $post->user->name)}}">{{$post->user->name}}</a></div>
                            <p>{{$post->content}}</p>
                            <div id="add_delete">

                            </div>
                            <div>Comments</div>
                            <form method="post" id="addCommentForm" class="form-horizontal" enctype="multipart/form-data">

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input placeholder="Comment..." type="text" name="comment" id="comment" class="form-control" />
                                        <br>
                                        <input type="hidden" name="hidden_id" id="hidden_id">
                                        <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Add comment" />
                                    </div>
                                </div>

                            </form>
                            <hr>
                            <div id="commentList">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
<script>
    $(document).ready(function () {
        var _token = $('input[name="_token"]').val();
        var postId = '{{$post->id}}';
        showComments();

        $('#addCommentForm').on('submit', function (event) {
            event.preventDefault();
            var comment = $('#comment').val();
            console.log(comment);
            $.ajax({
                url: '{{route('comment.add')}}',
                method: 'POST',
                data:{postId:postId, comment:comment, _token:_token},
                dataType: 'json',
                success:function(result){
                    if(result === 'success'){
                    showComments();
                    alert('Commend added successful');
                    }
                    else alert('Please register or login');
                }
            })

        });

        function showComments() {
            $.ajax({
                url: '{{route('comment.show')}}',
                method: 'POST',
                data:{postId:postId, _token:_token},
                dataType: 'json',
                success:function(comments){
                    var result = '';
                    for(var count = 0; count < comments.length; count++) {
                        result += '<div><a href="'+comments[count].authorProfile+'">' + comments[count].author + '</a>';
                        result += '<p>'+comments[count].content+'</p></div><hr>'
                    }
                    $('#commentList').html(result);
                }
            });
        }

        inFavorite();
        function inFavorite(doAction = '') {

            $.ajax({
                url:"{{route('post.favorite')}}",
                method: "POST",
                data:{postId:postId, doAction:doAction, _token:_token},
                dataType:"json",
                success:function (data) {
                    var output = '';
                    if(data.canDelete == '1'){
                        output += '<a class="btn-danger action" id="action_btn">Delete from favorite</a>';
                    }
                    else{
                        output += '<a class="btn-info action" id="action_btn">Add to favorite</a>';
                    }
                    $('#add_delete').html(output);
                }
            });
        }
        $(document).on('click', '.action', function () {
            var val = this.id;
            inFavorite(val);
        });

    });
</script>
