<?php 

class Server_v2 {
    public static $max_client = 2;
    public static $server_address = '127.0.0.1';
    public static $server_port = 4748;
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
        if(socket_bind($socket, self::$server_address, self::$server_port) === false)
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
        
        //Initialization
        $client_list = array(); 
        $read_list = array();
        $write_list = array();

        // Looply socket_select
        echo "Accept... \n";
        while(true)
        {
            //prepare array of readable client sockets
            $read_list = array();
            //first socket is the master socket
            $read_list[0] = $socket;
            // add client into read_list
            for($i = 0; $i < self::$max_client; $i++)
            {
                if(isset($client_list[$i]))
                {
                    $read_list[] = $client_list[$i];
                }
            }

            // Start socket_select()
            $select_result = socket_select($read_list, $write_list, $except, null);
            if($select_result === false)
            {
                $errorcode = socket_last_error();
                $errormsg = socket_strerror($errorcode);
                die("Could not listen on socket : [$errorcode] $errormsg \n");
            }

            var_dump($select_result);
         
            // looply accept connections
            for ($i = 0; $i < self::$max_client; $i++)
            {
                // if connection come, Add to client_list
                if(isset($client_list[$i]) === false)
                {
                    $client_list[$i] = socket_accept($socket);
                    // Display connection info
                    if (socket_getpeername($client_list[$i], $address, $port))
                    {
                        echo "Connection from $address: $port \n";
                    }

                    // return ack
                    $ack_message = "You have connected to server \n";
                    socket_write($client_list[$i], $ack_message);
                    break;
                }
            }

            $count = count($client_list);
            echo "We have $count clients \n";
            // If select_result > 0, try read in a loop
            for($i = 0; $i < self::$max_client; $i++)
            {
                //if read something, process
                if($i < count($client_list) && in_array($client_list[$i], $read_list))
                {
                    // start socket_read
                    $read_result = socket_read($client_list[$i], 1024);
                    // if read nothing, that mean disconnection happens,remove and close the socket
                    if($read_result == null)
                    {
                        unset($client_socks[$i]);
                        socket_close($client_socks[$i]);
                    }

                    $input = trim($read_result);
                    socket_getpeername($client_list[$i], $address, $port);
                    echo "Get from $address:$port: $input \n";
                    // Prepare data
                    $send_data = "Message from {$address}::{$port}: {$input}\n";
                    // then broadcast to other clients
                    foreach($client_list as $j => $client2)
                    {
                        if($i != $j)
                        {
                            socket_write($client2, $send_data);
                        }
                    }
                }
                // if write something, process
            }
        }
    }
}

Server_v2::main();
//End