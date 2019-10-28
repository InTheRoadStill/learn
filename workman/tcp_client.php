<?php
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

$worker = new Worker();
// $worker->onWorkerStart = function()
// {
    // Websocket protocol for client.
    $ws_connection = new AsyncTcpConnection("127.0.0.1:1234");
    $ws_connection->onConnect = function($connection){
        $connection->send('hello');
    };
    $ws_connection->onMessage = function($connection, $data){
        echo "recv: $data\n";
        $connection->close();
    };
    $ws_connection->onError = function($connection, $code, $msg){
        echo "error: $msg\n";
    };
    $ws_connection->onClose = function($connection){
        echo "connection closed\n";
    };
    $ws_connection->connect();
// };
Worker::runAll();