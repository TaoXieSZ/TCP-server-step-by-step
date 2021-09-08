<?php 

class Server_v2 {
    public static function main()
    {
        // Create socket, display when error happens
        $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        if($socket === false)
        {
            echo 'Create socket failly';
            return false;
        }
        // Bind socket to an address <ip, port>
        if(socket_bind($socket, '127.0.0.1', 1249) === false)
        {
            echo 'Bind error';
            return false;
        }
        // listen on the port 
        if (socket_listen($socket) === false)
        {
            echo 'Listen error';
            return false;
        }

        echo "Waiting for coming connetion \n";
        
        // accept connection
        echo "Accept... \n";
        while(true)
        {
            $connection = socket_accept($socket);
            if ($connection === false)
            {
                echo 'Accept error';
                return false;
            }
            if (socket_getpeername($connection, $address, $port))
            {
                echo "Connection from $address:$port \n";
            }
            $input = socket_read($connection, 10240);
            socket_write($connection, "你很牛啊\n");
        }
    }
}

Server_v2::main();
//End