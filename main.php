<?php
include("./autoload.php");
$http = new HTTP2((new Gear())->run($userHash));
$http->init('10.4.13.103', '8001')->start();