<?php
include(dirname(__FILE__) . '/autoload.php');
$s = new EzServerMaster();
$s->init('127.0.0.1', '8888', './')->run();