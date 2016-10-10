<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <style type="text/css">
        .addFriend-dl{
            display: none;
            position: absolute ;
            z-index: 9999;
            left: 5%;
            width: 90%;
            background: white;
        }
        .addFriend-dt{
            cursor:pointer;
            padding: 2% 0%;
        }
        @yield('style')
    </style>
    <!-- Scripts -->
    <script src="//cdn.bootcss.com/socket.io/1.4.8/socket.io.min.js"></script>
    <script src="//cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        退出
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a  data-toggle="modal" data-target="#addFriend">添加好友</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal -->
    <div class="modal fade" id="addFriend" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="height: 100%;">
        <div class="modal-dialog" id="addFriend-modal-dialog" style="height: 50%;">
            <div class="modal-content" style="height: 95%;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="addFriend-myModalLabel">添加好友</h4>
                </div>
                <div id="addFriend-modal-body" class="modal-body" style="height: 80%;">
                    <div class="form-group">
                        <div class="row">
                            <input type="hidden" class="form-control" id="friendId">
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="friendName" placeholder="请输入好友昵称检索">
                                <dl class="addFriend-dl" id="addFriend-dl">

                                </dl>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" id="addFriend-btn" class="btn btn-default">添加</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('content')

    <!-- Scripts -->
    <script src="/js/app.js"></script>
    <script type="text/javascript">
        $(function (){
            $("#friendName").keyup(function (){
                $('#addFriend-dl').html('');
                var friendName = $(this).val();
                if(friendName=='')
                    return false;
                $.ajax({
                    url:'{{ route('getUserInfo') }}',
                    type:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{friend_name:friendName},
                    dataType:'json',
                    success:function (data){
                        var users = data.data;
                        if(users.length<=0){
                            $('#addFriend-dl').html('<dt>没有找到对应好友</dt>');
                            $('#addFriend-dl').show();
                            return false;
                        }
                        for(var i=0;i<users.length;i++){
                            $('#addFriend-dl').html('');
                            $('#addFriend-dl').append('<dt class="addFriend-dt" user_id="'+users[i].id+'" user_name="'+users[i].name+'">'+users[i].name+'</dt>');
                            $('#addFriend-dl').show();
                        }
                        $('.addFriend-dt').click(function (){
                            $('#friendId').val($(this).attr('user_id'));
                            $('#friendName').val($(this).attr('user_name'));
                            $('#addFriend-dl').html('');
                            $('#addFriend-dl').hide();
                        });
                    }
                });

            });
            $('#addFriend-btn').click(function (){
                var friendId = $('#friendId').val().trim();
                var friendName = $('#friendName').val().trim();
                if(friendId==''||friendName==''){
                    alert('必要参数不能为空！');
                    return false;
                }
                $.ajax({
                    url:'{{ route('addFriend') }}',
                    type:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{friend_id:friendId,friend_name:friendName},
                    dataType:'json',
                    success:function (data){
                        init();
                        $('.close').click();
                        alert('添加好友成功！');
                    }
                });
            })
        })
    </script>
</body>
</html>
