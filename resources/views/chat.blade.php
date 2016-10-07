<div class="modal-content" style="height: 95%;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ $name }}</h4>
    </div>
    <div id="modal-body" class="modal-body" style="height: 80%;">
            <div class="row" style="height: 70%;">
                <div class="col-md-12"  style="height: 100%;">
                    <div class="panel panel-default"  style="height: 100%;">
                        <div class="panel-heading">正在与->{{ $name }}<-聊天</div>
                        <div class="panel-body" id="message_content-{{ $id }}"  style="overflow-y:scroll;height: 85%;">
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
    <div class="modal-footer">
        <button type="button" id="close" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>

</div>
    <script>
        $(function (){
            var socket=io('192.168.1.223:3000');
            $.ajax({
                url:'{{ route('getMessageList') }}',
                type:'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{from_user_id:'{{ $id }}'},
                dataType:'json',
                success:function (data){
                    for(var i =0 , messages=data.data.messages ; i<messages.length;i++){
                        if(messages[i].from_user.id=={{ auth()->user()->id }})
                            $('#message_content-'+messages[i].to_user.id).append('<dl style="text-align: right"><dt><span style="padding-right: 2%">'+messages[i].from_user.name+'</span><small>'+messages[i].created_at+'</small></dt><dd>'+messages[i].messages+'</dd></dl>');
                        $('#message_content-'+messages[i].from_user.id).append('<dl><dt><span style="padding-right: 2%">'+messages[i].from_user.name+'</span><small>'+messages[i].created_at+'</small></dt><dd>'+messages[i].messages+'</dd></dl>');
                    }
                    $('#message_content-{{ $id }}').scrollTop($('#message_content-{{ $id }}').get(0).scrollHeight);
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
                    data:{to_user_id:'{{ $id }}',message:message},
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
