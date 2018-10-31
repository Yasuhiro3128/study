<?php
$stdin = fopen('php://stdin', 'r');
stream_set_blocking($stdin, false);

while (true) {

    $in = stream_get_contents($stdin);

    if ($in) {
          echo $in;
    }

    sleep(1);
    echo '.';

}
