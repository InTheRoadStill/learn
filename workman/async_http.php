<?php
require_once __DIR__ . '/vendor/autoload.php';
$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);
echo time();
$request = $client->request('GET', 'http://192.168.4.220:801/test/');
$request->on('response', function ($response) {
    $response->on('data', function ($chunk) {
        echo "ok";
    });
    $response->on('end', function() {
        echo 'DONE';
    });
});
$request->on('error', function (\Exception $e) {
    echo $e;
});
$request->end();
$arr = array();
$request1 = $client->request('GET', 'https://github.com');
$request1->on('response', function ($response) use(&$content) {
	$content = '';
    $response->on('data', function ($chunk) use(&$content) {
        $content .= $chunk;
    });
    $response->on('end', function() use(&$content) {
    	echo "get content\r\n";
        file_put_contents('./1.html', $content);
    });
});
$request1->on('error', function (\Exception $e) {
    echo $e;
});
$request1->end();

echo "begin";

$loop->run();
echo "\r\n";
echo time();