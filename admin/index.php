<?php
namespace gear;
use gear\web\app;
include(__DIR__.'/../gear/autoload.php');
$config = '';//__DIR__.'/config/';
$app = new App($config);
$app->initWebApp();