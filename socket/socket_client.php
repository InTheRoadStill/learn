<?php
set_time_limit(0);
//打开连接
$fp = fsockopen("192.168.4.176", 10008, $errno, $errstr, 10);
if(!$fp) exit("CONNECT ERROR");
$filename = './CentOS-6.6-x86_64-bin-DVD1.iso';
read_file($filename);
fclose($fp);
function read_file($filename){
	global $fp;
	$handle = fopen($filename, "r");
	if(!$handle) fclose($fp);
	while(!feof($handle)){
		fwrite($fp, fgets($handle));
	}
}