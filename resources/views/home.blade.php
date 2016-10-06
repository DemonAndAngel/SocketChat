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
    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
        Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(function (){
            var socket=io('192.168.1.223:3000');
            socket.on('channel-message:App\\Events\\ANewMessage:{{ auth()->user()->id }}',function (data){
                {{--$.ajax({--}}
                    {{--url:'{{ route('setMessage') }}',--}}
                    {{--type:'post',--}}
                    {{--headers: {--}}
                        {{--'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
                    {{--},--}}
                    {{--data:{message_id:data.message.id},--}}
                    {{--dataType:'json',--}}
                    {{--success:function (data){--}}

                    {{--}--}}
                {{--});--}}
                $('#count-'+data.fromUser.id).html(Number($('#count-'+data.fromUser.id).html())+1);
                $('#message-'+data.fromUser.id).html(data.message.messages);
                $('#time-'+data.fromUser.id).html(data.message.created_at);
            });
            {{--socket.on('channel-message:App\\Events\\ANewMessage:{{ auth()->user()->id }}:send',function (data){--}}
                {{--$('#content').append('<dl style="text-align: right"><dt><span style="padding-right: 2%">'+data.fromUser.name+'</span><small>'+data.message.created_at+'</small></dt><dd>'+data.message.messages+'</dd></dl>');--}}
            {{--});--}}
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
                        htmlArr.push('<div class="panel-body content" to_user_id="'+data.data[i].id+'"><div class="col-sm-12"><span class="radius" id="count-'+data.data[i].id+'" >0</span><strong>'+data.data[i].name+'</strong></div><div class="col-sm-7"  id="message-'+data.data[i].id+'" ></div><div class="col-sm-5 text-right"  id="time-'+data.data[i].id+'"></div></div>');
                    }
                    $('#content').append(htmlArr.join(''));
                    $('.content').click(function (){
                        alert('123');
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
                }
            });
        });
    </script>
@endsection
