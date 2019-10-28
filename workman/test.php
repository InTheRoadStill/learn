<?php

$a = 1;
$changeVar = function()use(&$a){
	$a = 2;
};

$getVar = function()use($a){
	echo $a;
};

$changeVar();

echo $a.PHP_EOL;

$getVar();
