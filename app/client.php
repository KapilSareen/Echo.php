<?php 
if(isset($_POST["submit"])){

    $host="127.0.0.1";
    $port= 8005;
    $socket=socket_create(  AF_INET, SOCK_STREAM, 0) or die("Not created");
    socket_connect($socket, $host, $port);
    $msg=$_POST['msg'];
    socket_write( $socket,"$msg",strlen($msg));

    $reply=socket_read($socket,1024);
    echo"$reply";
    socket_close($socket);
}
?>
<form action="" method="post">
<input type="text" name="msg" id="" required>
<input type="submit" value="Submit" name='submit'></form>