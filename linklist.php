<?php

Class node {
	public $data;
	public $next;

	function __construct($data,$next){
		$this->data = $data;
		$this->next = $next;
	}
}

Class LinkList {

	public $firstNode; //头部
	public $count = 0; //元素个数

	public function add($data){
		return $this->addNode($this->count, $data);
	}

	public function get($index){
		$node = $this->getNode($index);
		return $node->data ?? false; 
	}

	private function getNode($index){
		$i = 0;
		$node = $this->firstNode;
		while($i<=$index && $i < $this->count && $node){
			if($index == $i){
				return $node;
			}
			$node = $node->next;
			$i++;
		}
		return false;
	}

	private function addNode($index, $data){
		 $preNode  = $this->getNode($index-1);
		 $nextNode = $this->getNode($index);
		 $node = new node($data, null);
		 if($preNode) $preNode->next = $node;
		 $node->next = $nextNode;
		 if($index == 0) $this->firstNode = $node;
		 $this->count++;
		 return true;
	}

	public function edit($index, $data){
		$i = 0;
		$node = $this->firstNode;
		if(!$node) return false;
		while($i<=$index && $i < $this->count){
			if($index == $i){
				$node->data = $data;
				return true;
			}
			$node = $node->next;
			$i++;
		}
	}

	public function delete($index){
		$i = 0;
		$preNode = null;
		$node = $this->firstNode;
		$nextNode = $node->next;
		while($i<=$index && $i < $this->count){
			if($index == $i){
				if($index == ($this->count -1)){
					$this->end = $preNode;
					if($this->end) $this->end->next = null;
				}
				if($index == 0) $this->firstNode = $nextNode;
				if($preNode){
					$preNode->next = $nextNode;
				}else{
					$this->firstNode = $nextNode;
				}
				unset($node);
				$this->count--;
				return true;
			}
			$preNode = $node;
			$node = $node->next;
			$nextNode = $node->next??null;
			$i++;
		}
		return false;
	}

	public function unshift($data){
		return $this->addNode(0, $data);
	}

	public function pop(){
		if(!$this->count) return false;
		$index = $this->count-1;
		$data = $this->get($index);
		$this->delete($index);
		return $data;
	}

	public function getCount(){

		return $this->count;
	}
}

$linklist = new LinkList();
// $linklist->add("1");
// $linklist->add("2");
$linklist->add("3");
// echo $linklist->pop().PHP_EOL;
// echo $linklist->get(0).PHP_EOL;
var_dump($linklist->delete(0));
// echo $linklist->pop().PHP_EOL;
$linklist->add("1");
$linklist->add("2");
$linklist->add("3");
$linklist->add("4");
$linklist->unshift("6");
$linklist->unshift("5");
$linklist->delete(0);
$linklist->delete(10);
$linklist->edit(4, 999);
echo $linklist->pop().PHP_EOL;
echo $linklist->pop().PHP_EOL;
echo $linklist->pop().PHP_EOL;
echo $linklist->pop().PHP_EOL;
echo $linklist->pop().PHP_EOL;
var_dump($linklist->getCount());


