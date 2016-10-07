/**
 * Created by deyu on 2016/9/22.
 */
var http=require('http').Server();
var io=require('socket.io')(http);
var Redis=require('ioredis');
var redis=new Redis('6379', '127.0.0.1');
redis.subscribe('channel-message');
redis.on('message',function (channel,data){
    data = JSON.parse(data);
    //这些事全局的
    io.emit(channel+':'+data.event+':'+data.data.message.to_user_id,data.data);
    io.emit(channel+':'+data.event+':'+data.data.message.from_user_id+':send',data.data);
    //这些是局部的
    io.emit(channel+':'+data.event+':'+data.data.message.to_user_id+':'+data.data.message.from_user_id,data.data);
    io.emit(channel+':'+data.event+':'+data.data.message.from_user_id+':send:'+data.data.message.to_user_id,data.data);

});
http.listen(3000,function (){
    console.log('Server Start');
});
