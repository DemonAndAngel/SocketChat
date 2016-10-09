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
    .content{
        border:3px solid #c0fffc
    }
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">列表</div>
                <div class="panel-body" id="content" >
                </div>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->
    <span id="span" data-toggle="modal" data-target="#myModal"></span>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="height: 100%;">
        <div class="modal-dialog myModal" id="modal-dialog" style="height: 100%;">

        </div>
    </div>
</div>
    <script>
        var NOWUSERID=0;
        function init(){
            $('#content').html('');
            $.ajax({
                url:'{{ route('getFriendList') }}',
                type:'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (data){
                    if(data.meta.code!=200)
                        return false;
                    var htmlArr=[];
                    for(var i=0;i<data.data.length;i++){
                        var toUser = data.data[i];
                        $.ajax({
                            url:'{{ route('getMessageListInfo') }}',
                            type:'get',
                            data:{'from_user_id':toUser.id},
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType:'json',
                            success:function (data){
                                var message = data.data.message;
                                if(!message){
                                    message={};
                                    message.created_at='';
                                    message.messages='';
                                }
                                htmlArr.push('<div class="panel-body content" to_user_id="'+toUser.id+'"><div class="col-sm-12"><span class="radius" id="count-'+toUser.id+'" >'+data.data.count+'</span><strong>'+toUser.name+'</strong></div><div class="col-sm-7"  id="message-'+toUser.id+'" >'+message.messages+'</div><div class="col-sm-5 text-right"  id="time-'+toUser.id+'">'+message.created_at+'</div></div>');
                                $('#content').append(htmlArr.join(''));
                                $('.content').click(function (){
                                    var to_user_id = $(this).attr('to_user_id').trim();
                                    NOWUSERID = to_user_id;
                                    $('#modal-dialog').html('<div style="text-align: center;padding-top: 50%;"><img src="img/loading_content.gif"></div>');
                                    $('#span').click();
                                    $.ajax({
                                        url:'{{ route('setMessage') }}',
                                        type:'post',
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data:{from_user_id:to_user_id},
                                        dataType:'json',
                                        success:function (data){
                                            $('#count-'+to_user_id).html(0);
                                            $.ajax({
                                                url:'{{ route("chatPage") }}',
                                                type:'get',
                                                data:{'to_user_id':to_user_id},
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                },
                                                success:function (data){
                                                    $('#modal-dialog').html(data);
                                                }
                                            });
                                        }
                                    });
                                });
                            }
                        });
                    }
                }
            });
        }
        $(function (){
            init();
            var socket=io('192.168.1.223:3000');
            socket.on('channel-message:App\\Events\\ANewMessage:{{ auth()->user()->id }}',function (data){
                if(NOWUSERID==data.fromUser.id){
                    $.ajax({
                        url:'{{ route('setMessageForId') }}',
                        type:'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{message_id:data.message.id},
                        dataType:'json',
                        success:function (data2){
                        }
                    });
                    $('#message_content-'+data.fromUser.id).append('<dl><dt><span style="padding-right: 2%">'+data.fromUser.name+'</span><small>'+data.message.created_at+'</small></dt><dd>'+data.message.messages+'</dd></dl>');
                }
                if(data.message.is_receive==0&&NOWUSERID!=data.fromUser.id)
                    $('#count-'+data.fromUser.id).html(Number($('#count-'+data.fromUser.id).html())+1);
                $('#message-'+data.fromUser.id).html(data.message.messages);
                $('#time-'+data.fromUser.id).html(data.message.created_at);
            });
            socket.on('channel-message:App\\Events\\ANewMessage:{{ auth()->user()->id }}:send',function (data){
                $('#message-'+data.toUser.id).html(data.message.messages);
                $('#time-'+data.toUser.id).html(data.message.created_at);
                if(NOWUSERID==data.toUser.id)
                $('#message_content-'+data.toUser.id).append('<dl style="text-align: right"><dt><span style="padding-right: 2%">'+data.fromUser.name+'</span><small>'+data.message.created_at+'</small></dt><dd>'+data.message.messages+'</dd></dl>');
            });
            $('#myModal').on('hide.bs.modal', function () {
                NOWUSERID=0;
                $('#modal-dialog').html('');
            })
        });
    </script>
@endsection
