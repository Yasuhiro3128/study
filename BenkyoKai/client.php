<?php

$test_servaddr = '192.168.0.1'; //server address
$test_servport = 10000; //port

//socket:create→connect
if(($test_sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false){ 
  echo "socket_create() error:". socket_strerror(socket_last_error());
}

if((socket_connect($test_sock, $test_servaddr, $test_servport)) === false){
  echo "socket_connect() error:".socket_strerror(socket_last_error());
}


while($test_inputbuf = fgets(STDIN,2048)){

  if(!($test_inputbuf = trim($test_inputbuf))){
    continue;
  }

  //output and create message
  echo "write:".$test_inputbuf."\n";
  $test_writebuf = $test_inputbuf."\n";

  //write message
  if(socket_write($test_sock, $test_writebuf, strlen($test_writebuf)) === false){
    echo "socket_write()  error:".socket_strerror(socket_last_error());
  }

  //end of connection
  switch($test_inputbuf) {

  case 'quit':
    socket_close($test_sock);
    break 2;

  case 'shutdown':
    socket_close($test_sock);
    break 2;

  default :
    break;

  }

  //read and output message
  if(($test_readbuf = socket_read($test_sock, 2048, PHP_NORMAL_READ)) === false){
    echo "socket_read() error:". socket_strerror(socket_last_error());
  }
  else{
    echo "read:".$test_readbuf;
  }

}

?>
