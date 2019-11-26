<?php
include("./autoload.php");
$http = new HTTP((new Gear())->run($userHash));
$http->init('0.0.0.0', '8000')->start();