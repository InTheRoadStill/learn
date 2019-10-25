<?php
// 建立server端socket  
ini_set('memory_limit', '1024M');
$tcp = getprotobyname("tcp");  
$socket = socket_create(AF_INET, SOCK_STREAM, $tcp);
if(!$socket) exit(' -ERROR  is useing by other server.');
if(!socket_bind($socket, '0.0.0.0', 10008)) exit(' -ERROR  is useing by other server.');       //绑定要监听的端口  
socket_listen($socket);       //监听端口
echo 'START OK...'.PHP_EOL;
echo 'LISTENING'.PHP_EOL;
while(true){
	echo '----------------------------------------------------------------'.PHP_EOL;
	$connection = socket_accept($socket);
	if(!$connection){
		echo 'connect fail';
	}else{
		$str  = '';
		echo 'do something'.date('Y-m-d H:i:s',time()).PHP_EOL;
		$str = '';
		touch('./CentOS-6.6-x86_64-bin-DVD1.iso');
		$fp = fopen('./CentOS-6.6-x86_64-bin-DVD1.iso',"a+");
		while(@socket_recv($connection,$data,2048,0)){
			$str += strlen($data);
			fwrite($fp,$data);
			echo "getlent:".$str.PHP_EOL;
		}
		fclose($fp);
		echo 'DATASIZE:'.$str.PHP_EOL;
		$data = $str;
		echo 'READY TO SEND MSG!'.PHP_EOL;
		socket_write($connection, "connect ok! \n");  
	}
	socket_close($connection);
	sleep(3);
	echo 'END TIME:'.date('Y-m-d H:i:s',time()).PHP_EOL;
	echo '----------------------------------------------------------------'.PHP_EOL;
}