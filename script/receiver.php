<?php

define('ROOT_PATH',realpath(__DIR__ . "/.."));

require_once ROOT_PATH . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$config = require_once ROOT_PATH."/bootstrap/config.php";


$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('email_queue', false, false, false, false);


$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

$channel->basic_consume('email_queue', '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

