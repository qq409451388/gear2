<?php
include("./autoload.php");
$http = new HTTP((new Gear())->run($userClass));
$http->init('0.0.0.0', '8000')->start();
