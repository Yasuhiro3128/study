<?php
error_reporting(E_ALL);

//Allow  the script to hang
set_time_limit(0);

//enables flush
ob_implicit_flush();

$address = '192.168.0.1'; //addr
$port = 10000; //port
$backlog = 5;
$bufsize = 2048;

//socket:create→bind→listen
if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false){
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false){
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, $backlog) === false){
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

//nonblockmode
if(socket_set_nonblock($sock) === false){
  echo "socket_set_nonblock() failed: reason: ". socket_strerror(socket_last_error($sock)). "\n";
}

//clients list
$clients = array($sock);

//for select
$read_sockets = array();
$write_sockets = NULL;
$error_sockets = NULL;
$select_interval = 0; //second

while(true){
　
  $read_sockets = $clients;

  //read only
  $select = socket_select($read_sockets, $write_sockets,
			  $error_sockets, $select_interval);
  
  //new client or new message
  if($select >= 1){
    
    //new client
    if(in_array($sock, $read_sockets)){
      
      //add client
      $clients[] = $newsock = socket_accept($sock);
      
      //delete new client from read list
      $clients_key = array_search ($sock, $read_sockets);
      unset($read_sockets[$clients_key]);      
      
    }

    //read message
    foreach($read_sockets as $read_key => $read_socket){

      //reaf failed
      if(($read_buf = socket_read($read_socket, $bufsize, PHP_NORMAL_READ)) === false){

	echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($read_socket)) . "\n";

　　　　//delete client
	$clients_key = array_search($read_socket, $clients);
	unset($read_sockets[$read_key]);
	unset($clients[$clients_key]);

      }

      //invalied message
      else if (!$read_buf = trim($read_buf)){

        //delete client
	$clients_key = array_search($read_socket, $clients);
	unset($read_sockets[$read_key]);
	unset($clients[$clients_key]);
	
        continue;

      }

      echo $read_buf."\n";
      
      //end of connection 
      if ($read_buf == 'quit'){

	$clients_key = array_search($read_socket, $clients);
	unset($read_sockets[$read_key]);
	unset($clients[$clients_key]);

      }

      //shutdown server
      else if ($read_buf == 'shutdown'){

        //delete all client
	foreach($clients as $clients_key => $client){ 

	  if($sock != $client){
	    unset($client);
	  }

	}
	break 2;

      }

      //echo back
      else{ 

        //write message
	$write_buf = $read_buf."\n";
	if(socket_write($read_socket, $write_buf, strlen($write_buf)) === false){

	  echo "socket_write() failed: rason: " . socket_strerror(socket_last_error($read_sock));

	  $clients_key = array_search($read_socket, $clients);
	  unset($read_sockets[$read_key]);
	  unset($clients[$clients_key]);

	}
      }
    }
  }

  //There is no readable socket
  else if($select == 0){
    continue;
  }
  
  //select error
  else if($select === false){

    echo "socket_select() failed: reason: ". sockets_strerror(socket_last_error($read_sock)) . "\n";

    break;
  }
  
}

//close socket
socket_close($sock);

?>
