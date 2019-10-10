<?php
namespace admin;

use gear\web\app;
include(dirname(__FILE__).'/../gear/autoload.php');
$config = '';//__DIR__.'/config/';
$app = new App($config);
$app->initWebApp();
