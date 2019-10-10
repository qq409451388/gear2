<?php
include(dirname(__FILE__) . '/autoload.php');
$config = '';//__DIR__.'/config/';
$app = new App($config);
$app->initWebApp();
