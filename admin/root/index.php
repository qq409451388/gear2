<?php
include(dirname(__FILE__) . '/autoload.php');
$config = '';//__DIR__.'/config/';
$app = new MyWebApp($config);
$app->initWebApp();
