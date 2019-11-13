<?php

$action = $argv[1] ?? "";

$masterPidFile = "/tmp/master.pid";

$setting = [
	'worker_num' => 4,
	'task_worker_num' => 3,
	'backlog' => 128,
];

switch($action){
	case 'start':
		# 是否存在 -d
		if(isset($argv[2]) && $argv[2] == "-d"){
			$setting['daemonize'] = true;
		}
		$serv = new Swoole\Http\Server("0.0.0.0",8888);
		$serv->set($setting);
		$serv->on('WorkerStart', 'my_onWorkerStart');
		$serv->on('Task', 'my_onTask');
		$serv->on('Start', 'my_onStart');
		$serv->on('Request', 'my_onRequest');
		$serv->start();
		break;

	case 'stop':
		$master_id = file_get_contents($masterPidFile);
		@unlink($masterPidFile);
		posix_kill($master_id, SIGTERM);
		echo "stop success... pid is".$master_id;
		break;

	case 'reload':
		$master_id = file_get_contents($masterPidFile);
		posix_kill($master_id, SIGUSR1);
		echo "reload success... pid is".$master_id;
		break;

	default:
		echo "no this action...".PHP_EOL;
		echo "you can use start|start -d|stop".PHP_EOL;
		break;
}

function my_onTask(swoole_server $serv, int $task_id, int $src_worker_id, $data){
	$flag = uniqid();
	file_put_contents("tmp/task.log", "[".$flag."]".date('Y-m-d H:i:s', time())."get task...".$data.PHP_EOL, FILE_APPEND);
	sleep(10);
	file_put_contents("tmp/task.log", "[".$flag."]".date('Y-m-d H:i:s', time())."get task...".$data.PHP_EOL, FILE_APPEND);
	echo "get task...".$data.PHP_EOL;
}

function my_onWorkerStart(swoole_server $server, int $worker_id){
	echo $worker_id.PHP_EOL;
}

function my_onStart(Swoole\Http\Server $server) {
	global $masterPidFile;
	swoole_set_process_name("cheng swoole master");
	$master_id = $server->master_pid;
	file_put_contents($masterPidFile, $master_id);
	echo "START SUCCESS... listening 8888 port".PHP_EOL;
}

function my_onConnect(swoole_server $server, int $fd, int $reactorId){
	echo "new connect...".PHP_EOL;
}

function my_onRequest($request, $response) {
	global $serv;
	if(isset($request->get['hello'])) $serv->task($request->get['hello']);
	require_once "/home/www/swoole/worker.php";
	$data = deal($request->get['hello']);
	$response->end($data);
}