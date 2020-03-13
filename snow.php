<?php
// 雪花算法

// !!!!! 只适用于PHP 64位

// 第一个部分，是 1 个 bit：0，这个是无意义的。

// 第二个部分是 41 个 bit：表示的是时间戳。

// 第三个部分是 5 个 bit：表示的是机房 id，10001。

// 第四个部分是 5 个 bit：表示的是机器 id，1 1001。

// 第五个部分是 12 个 bit：表示的序号，就是某个机房某台机器上这一毫秒内同时生成的 id 的序号，0000 00000000。

class snow {
	public $workid = 1;
	public $jifang = 1;
	public $inrc = 1;
	public $time = 1288834974657;

	public function __construct(){
		if($this->workid > 31 || $this->workid < 0 ){
			die('workid 范围不对');
		}
		if($this->workid > 31 || $this->workid < 0 ){
			die('jifang 范围不对');
		}
		// 只取五位
		$this->workid = $this->workid & 31;
		$this->jifang = $this->jifang & 31;
	}

	public function make(){
		// 获取时间戳毫秒
		$timestamp = $this->getTimestamp();

		// 获取序列号
		$serialno = $this->getSerialNo($timestamp);

		return (($timestamp - $this->time) << 22) | ($this->jifang << 17) |  ($this->workid << 12) | $serialno;
	}

	public function getTimestamp() {
		list($msec, $sec) = explode(' ', microtime());
		$msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
		return $msectime;
	}

	public function getSerialNo($timestamp) {
		static $lasttimestamp;
		if($lasttimestamp == $timestamp){
			$this->inrc = ($this->inrc+1) & 4095;
			if($this->inrc == 0){
				$timestamp = getNextTime($timestamp);
			}
		}else{
			$this->inrc = 0;
		}
		$lasttimestamp = $timestamp;
		return $this->inrc;
	}

	public function getNextTime($timestamp){
		do{
			$newTimestamp = getTimestamp();
		}while($newTimestamp > $timestamp);
		return $newTimestamp;
	}
}

$snow = new snow();
for ($i=0; $i < 30; $i++) { 
	echo $snow->make().PHP_EOL;
}