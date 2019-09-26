<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('hello', false, false, false, false);

//Code to Handle User Input
echo "Enter the Message => ";
$handle = fopen ("php://stdin","r");
$mststr = trim(fgets($handle));
if($mststr == 'exit'){
    echo "ABORTING!\n";
    exit;
}
fclose($handle);

$msg = new AMQPMessage($mststr);
$channel->basic_publish($msg, '', 'hello');

echo "\n [x] Message Published \n\n";
$channel->close();
$connection->close();
?>