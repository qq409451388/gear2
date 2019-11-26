<?php
include("./autoload.php");
$http = new HTTP((new Gear())->run($userHash));
$http->init('10.4.13.103', '8091')->start();