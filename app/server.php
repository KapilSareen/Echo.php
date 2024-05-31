<?php
$host = "127.0.0.1";
$port = 8005;

$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
$result = socket_listen($socket, 3) or die("Could not set up socket listener\n");
echo "Server started on $host:$port\n";

do {
    # code...
    $client = socket_accept($socket) or die("Could not accept incoming connection\n");
    $msg = socket_read($client, 1024) or die("Could not read input\n");
    echo "Received message: $msg\n";
    echo"Enter Reply: ";
    $reply=fgets(STDIN);
    socket_write( $client,"$reply",strlen($reply));
    socket_close($client);
} while (true); 


