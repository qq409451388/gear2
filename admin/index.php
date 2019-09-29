<?php
include(__DIR__.'/../gear/autoload.php');
$config = __DIR__.'/config/';
$app = new App();
$app->initWebApp($config);