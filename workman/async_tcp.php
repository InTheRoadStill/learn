<?php
ini_set("display_errors", 1);
require_once __DIR__ . '/vendor/autoload.php';
use \Workerman\Worker;
use \Workerman\Connection\AsyncTcpConnection;

$task = new Worker();
// ��������ʱ�첽����һ����www.baidu.com���Ӷ��󣬲��������ݻ�ȡ����
$task->onWorkerStart = function($task)
{
    // ��֧��ֱ��ָ��http�����ǿ�����tcpģ��httpЭ�鷢������
    $connection_to_baidu = new AsyncTcpConnection('tcp://www.baidu.com:80');
    // �����ӽ����ɹ�ʱ������http��������
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

    // ��֧��ֱ��ָ��http�����ǿ�����tcpģ��httpЭ�鷢������
    $connection_to_221 = new AsyncTcpConnection('tcp://192.168.4.220:801');
    // �����ӽ����ɹ�ʱ������http��������
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

        // ��֧��ֱ��ָ��http�����ǿ�����tcpģ��httpЭ�鷢������
    $connection_to_220 = new AsyncTcpConnection('tcp://192.168.4.220:801');
    // �����ӽ����ɹ�ʱ������http��������
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

// ����worker
Worker::runAll();