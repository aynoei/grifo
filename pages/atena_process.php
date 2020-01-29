<?php

include dirname (__DIR__) . '/functions.php'; 
$grifo = new Grifo();

$u = getopt("u:");

echo $grifo->atena($u['u']);