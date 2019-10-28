<?php
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use \Workerman\Lib\Timer;

// #### create socket and listen 1234 port ####
$tcp_worker = new Worker("tcp://0.0.0.0:1234");

// 4 processes
$tcp_worker->count = 4;

// Emitted when new connection come
$tcp_worker->onConnect = function($connection)
{
    echo "New Connection\n";
};

// Emitted when data received
$tcp_worker->onMessage = function($connection, $data)
{
	$time_interval = 10;
	Timer::add($time_interval, function()use($connection, $data)
    {
        echo "task run\n";
    	$connection->send("hello $data \n");
    }, [], false);
    // send data to client
};

// Emitted when new connection come
$tcp_worker->onClose = function($connection)
{
    echo "Connection closed\n";
};

Worker::runAll();