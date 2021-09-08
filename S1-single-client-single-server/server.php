<?php

// Create socket, display when error happens
$socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
if($socket === false)
{
    echo 'Create socket failly';
}
var_dump($socket);

// Bind socket to an address <ip, port>

// listen on the port 

// while loop

    // accept the connection from client

    // process 

    // send back


// End