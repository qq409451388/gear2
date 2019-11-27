<?php
include("./autoload.php");
$http = new HTTP2((new Gear())->run($userHash));
$http->init('0.0.0.0', '8000')->start();
