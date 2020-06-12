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
                        <div class="card-header">My Posts</div>

{{--                        Modal Forms--}}

                        <div id="addModal" class="modal" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Add new post</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <span id="form_addPost_result"></span>
                                        <form method="post" id="addPostForm" class="form-horizontal" enctype="multipart/form-data">

                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <input placeholder="Category..." type="text" name="title" id="title" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <input placeholder="Content..." type="text" name="content" id="content" class="form-control" />
                                                </div>
                                            </div>

                                            <br />
                                            <div class="form-group" align="center">
                                                <input type="hidden" name="hidden_id" id="hidden_id">
                                                <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Add" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


{{--                        end modal forms--}}
                        {{ csrf_field() }}
                        <div class="card-body">
                            <button type="button" name="addBook" id="addBook" class="btn-info">Add new book</button>

                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th width="35%">Author</th>
                                    <th width="65%">Title</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
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
        var userId = '{{\Illuminate\Support\Facades\Auth::user()->id}}';
        var _token = $('input[name="_token"]').val();
        var id = '{{Auth::user()->id}}';
        myPosts(id);
        $('#addBook').click(function () {
            $('#addModal').modal('show');
        });

        function myPosts(id = ''){
            $.ajax({
                url:"{{ route('posts.show') }}",
                method: "POST",
                data:{id:id, _token:_token},
                dataType:"json",
                success:function (posts) {
                    var output = '';
                    for(var count = 0; count<posts.length; count++) {
                        output += '<tr>';
                        output += '<td ><a href="'+posts[count].authorProfile+'">' + posts[count].author + '</a></td>';
                        output += '<td ><a href="'+posts[count].link+'">' + posts[count].title + '</a></td>';
                        output += '<td ><a type="button" id="'+posts[count].id+'" class="btn btn-danger delete">delete</a></td></tr>';
                    }
                    $('tbody').html(output);
                }
            });
        }

        $('#addPostForm').on('submit', function (event) {
            event.preventDefault();
            var form_data = new FormData();
            form_data.append('title', $('#title').val());
            form_data.append('text', $('#content').val());
            form_data.append('_token', _token);
            $.ajax({
                url:"{{ route('post.createPost') }}",
                method:"POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                dataType:"json",
                success:function (data) {

                    var result;
                    if(data.success){
                        result = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#addPostForm')[0].reset();
                    }
                    $('#form_addPost_result').html(result);
                }
            });
        });

        $(document).on('click', '.delete', function () {
            var idToDelete = this.id;
            $.ajax({
                url: "{{route('post.delete')}}",
                method: "POST",
                data: {idToDelete:idToDelete, _token:_token},
                dataType:"json",
                success:function (result) {
                    if(result.success){
                        myPosts(id);
                        alert(result.success);
                    }
                }
            });
        });
    });
</script>
