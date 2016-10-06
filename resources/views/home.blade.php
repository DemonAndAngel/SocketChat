@extends('layouts.app')
@section('style')
    .radius{
        border-radius: 50%;
        background: #FF7500;
        color: #FFF;
        font-weight: bold;
        padding-left:0.5%;
        padding-right:0.5%;
        margin-right: 0.5%;
    }
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">列表</div>
                <div class="panel-body" id="content">
                    <div class="col-md-12"><span class="radius">3</span>姓名</div>
                    <div class="col-md-7">
                        内容
                    </div>
                    <div class="col-md-5 text-right">
                        2016-10-10 12:12:12
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(function (){
            var socket=io('192.168.1.223:3000');
            socket.on('channel-message:App\\Events\\ANewMessage:{{ auth()->user()->id }}',function (data){
                $.ajax({
                    url:'{{ route('setMessage') }}',
                    type:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{message_id:data.message.id},
                    dataType:'json',
                    success:function (data){

                    }
                });
                $('#content').append('<dl><dt><span style="padding-right: 2%">'+data.fromUser.name+'</span><small>'+data.message.created_at+'</small></dt><dd>'+data.message.messages+'</dd></dl>');
            });
            socket.on('channel-message:App\\Events\\ANewMessage:{{ auth()->user()->id }}:send',function (data){
                $('#content').append('<dl style="text-align: right"><dt><span style="padding-right: 2%">'+data.fromUser.name+'</span><small>'+data.message.created_at+'</small></dt><dd>'+data.message.messages+'</dd></dl>');
            });
            $.ajax({
                url:'{{ route('offLineMessage') }}',
                type:'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (data){

                }
            });
        });
    </script>
@endsection
