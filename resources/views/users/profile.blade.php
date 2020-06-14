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
                        <div class="card-header">{{$user->name}}</div>

                        {{ csrf_field() }}
                        <div class="card-body">
                            <div id="addOrDeleteFriend">

                            </div>
                            <div id="friends">

                            </div>
                            <div id="awaiting">

                            </div>
                          @if($user->id != \Illuminate\Support\Facades\Auth::user()->id)  <h3> Posts of this user: </h3>
                       @foreach($user->posts as $post)
                                <div> Title: <a href="{{route('postPage', $post->title)}}">{{$post->title}}</a>| Create at: {{$post->created_at}}
                                </div>
                                <hr>
                           @endforeach
                        @endif
                            <H3>Favorite posts:</H3>
                            @foreach($user->favoritePosts as $post)
                                <div>
                                    Author: <a href="{{route('profile.show', $post->user->name)}}">{{$post->user->name}}</a>
                                    <p>Title: <a href="{{route('postPage', $post->title)}}">{{$post->title}}</a>| Create at: {{$post->created_at}}</p>
                                    <hr>
                                </div>
                                @endforeach
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
        var userId = '{{$user->id}}';
        var _token = $('input[name="_token"]').val();
        var isMyProfile = '{{($user->id == \Illuminate\Support\Facades\Auth::user()->id ? 1 : 0)}}';
        if(isMyProfile == 0){
            addDeleteFriend();
            function addDeleteFriend(doAction = '') {
                $.ajax({
                   url: '{{route('profile.friend')}}',
                   method: 'POST',
                   data:{userId:userId, doAction:doAction, _token:_token},
                   dataType: 'json',
                    success:function (result) {
                       var output = '';
                       if(result.canAdd){
                           output += '<a class="btn-info action" id="add">Add to friends</a>';
                       }
                       else if(result.canDelete){
                           output += '<a class="btn-danger action" id="delete">Delete from friends</a>';
                       }
                       else if(result.answer){
                           output += '<a>User want to add you in friends</a>';
                           output += '<a class="btn-info action" id="add">Acceps</a>';
                           output += '<a class="btn-danger action" id="delete">Reject</a>';
                       }
                       else {
                           output += '<a>Awaiting answer from user</a>';
                       }
                        output += '<hr>';
                       $('#addOrDeleteFriend').html(output);
                       showUserFriends();
                    }
                });
            }
            $(document).on('click', '.action', function () {
                var val = this.id;
                addDeleteFriend(val);
            });
        }
        else{
            waitingAnswer();
            function waitingAnswer(doAction = '', id = '') {
                $.ajax({
                    url:'{{route('profile.awaitingAnswer')}}',
                    method: 'POST',
                    data:{_token:_token, doAction:doAction, id:id},
                    dataType: 'json',
                    success:function (awaiting) {
                        var output = '';
                        if(awaiting.length !== 0){
                            output += '<h3>Awaiting answer:</h3> ';
                            for(var count=0; count<awaiting.length; count++) {
                                output += '<a href="'+awaiting[count].link+'">'+awaiting[count].name+'</a>';
                                output += '<a class="btn-info add" id="'+awaiting[count].id+'">Acceps</a>';
                                output += '<a class="btn-danger delete" id="'+awaiting[count].id+'">Reject</a>';
                            }
                            output += '<hr>';
                        }
                        if(doAction === 'add'){
                            showUserFriends();
                        }
                        $('#awaiting').html(output);
                    }
                })
            }
            $(document).on('click', '.del', function () {
                var id = this.id;
                var action = 'del';
                waitingAnswer(action, id);
            });
            $(document).on('click', '.add', function () {
                var id = this.id;
                var action = 'add';
                waitingAnswer(action, id);
            });
        }
        showUserFriends();
        function showUserFriends() {
            $.ajax({
                url:'{{route('profile.showFriends')}}',
                method: 'POST',
                data:{userId:userId, _token:_token},
                dataType: 'json',
                success:function (friendList) {
                    var output = '';
                    if(friendList.length !== 0){
                        output += '<h3>Friends:</h3> ';
                        for(var count=0; count<friendList.length; count++) {
                            output += '<a href="'+friendList[count].link+'">'+friendList[count].name+'</a>';
                        }
                        output += '<hr>';
                    }
                    else{
                        output += '<h3>Friend list is empty</h3> ';
                        output += '<hr>';
                    }
                    $('#friends').html(output);
                }
            })
        }
    });
</script>
