<?php
ini_set("display_errors", 1);
require_once __DIR__ . '/vendor/autoload.php';
use \Workerman\Worker;
use \Workerman\Connection\AsyncTcpConnection;

$task = new Worker();
// 进程启动时异步建立一个到www.baidu.com连接对象，并发送数据获取数据
$task->onWorkerStart = function($task)
{
    // 不支持直接指定http，但是可以用tcp模拟http协议发送数据
    $connection_to_baidu = new AsyncTcpConnection('tcp://www.baidu.com:80');
    // 当连接建立成功时，发送http请求数据
    $connection_to_baidu->onConnect = function($connection_to_baidu)
    {
        echo "connect success\n";
        $connection_to_baidu->send("GET / HTTP/1.1\r\nHost: www.baidu.com\r\nConnection: keep-alive\r\n\r\n");
    };
    $connection_to_baidu->onMessage = function($connection_to_baidu, $http_buffer)
    {
        echo "baidu ok";
    };
    $connection_to_baidu->connect();

    // 不支持直接指定http，但是可以用tcp模拟http协议发送数据
    $connection_to_221 = new AsyncTcpConnection('tcp://192.168.4.220:801');
    // 当连接建立成功时，发送http请求数据
    $connection_to_221->onConnect = function($connection_to_221)
    {
        echo "connect success\n";
        $connection_to_221->send("GET /test/ HTTP/1.1\r\nHost: 192.168.4.220\r\nConnection: keep-alive\r\n\r\n");

    };
    $connection_to_221->onMessage = function($connection_to_221, $http_buffer)
    {
        echo "4.221 ok";
    };
    $connection_to_221->connect();

        // 不支持直接指定http，但是可以用tcp模拟http协议发送数据
    $connection_to_220 = new AsyncTcpConnection('tcp://192.168.4.220:801');
    // 当连接建立成功时，发送http请求数据
    $connection_to_220->onConnect = function($connection_to_220)
    {
        echo "connect success\n";
        $connection_to_220->send("GET /test/ HTTP/1.1\r\nHost: 192.168.4.220\r\nConnection: keep-alive\r\n\r\n");

    };
    $connection_to_220->onMessage = function($connection_to_220, $http_buffer)
    {
        echo "4.220 ok";
    };
    $connection_to_220->connect();
};

// 运行worker
Worker::runAll();