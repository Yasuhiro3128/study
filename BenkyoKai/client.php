<?php

$test_servaddr = '192.168.0.1'; //server address
$test_servport = 10000; //port


stream_set_blocking($test_sock, false);

$test_readbuf = fread($test_sock, 2048);

fwrite($test_sock, $test_writebuf, strlen($test_writebuf);

//socket:create→connect
$test_sock = stream_socket_client("tcp://$test_servaddr:$test_servport", $errno, $errstr);
if(!$test_sock){
  echo "$errstr ($errno)<br />\n";
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
