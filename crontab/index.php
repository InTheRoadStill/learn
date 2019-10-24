<?php
ini_set("display_errors", 1);
require './vendor/autoload.php';
use TiBeN\CrontabManager;
$crontabRepository = new TiBeN\CrontabManager\CrontabRepository(new TiBeN\CrontabManager\CrontabAdapter());
$crontabJob = TiBeN\CrontabManager\CrontabJob::createFromCrontabLine('* * * * * echo "hello1" > /tmp/123.log');
$crontabRepository->addJob($crontabJob);
$crontabRepository->persist();
var_dump($crontabJob);