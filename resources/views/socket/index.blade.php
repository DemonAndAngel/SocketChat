<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Socket</title>
</head>
<body>
<ul id="demo">
</ul>
<script src="//cdn.bootcss.com/socket.io/1.4.8/socket.io.min.js"></script>
<script src="//cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
<!--<script src="//cdn.bootcss.com/vue/2.0.0-rc.6/vue.min.js"></script>
<script>
    var socket=io('192.168.1.223:3000');
    new Vue({
        el:'#demo',
        data:{
            users:[]
        },
        ready:function (){
            socket.on('test-channel:aNewMessage',function (data){
                this.users.push(data.name);
            }.bind(this));
        }
    });
</script>
-->
<script>
    $(function (){
        var socket=io('192.168.1.223:3000');
        socket.on('test-channel:App\\Events\\ANewMessage',function (data){
            $('#demo').append($('<li>').append(data.name));
        });
    });
</script>
</body>
</html>