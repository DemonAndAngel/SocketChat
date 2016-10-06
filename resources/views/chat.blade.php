@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">内容页面</div>
                <div class="panel-body" id="content">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">消息发送</div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <textarea style="width:100%;" id="message"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button id="btn_sub" class="btn btn-success">发送</button>
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
            $('#btn_sub').click(function (){
                var message = $('#message').val().trim();
                if(!message){
                    alert('发送的消息不能为空');
                    return false;
                }
                $.ajax({
                    url:'{{ route('sendMessage') }}',
                    type:'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{message:message},
                    dataType:'json',
                    success:function (data){
                        if(data)
                            $('#message').val('');
                        else
                            alert('发送失败！');
                    }
                });
            });
        });
    </script>
@endsection
