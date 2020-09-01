<?php
if(is_file(dirname(__FILE__)."/autoload.php")){
    include(dirname(__FILE__)."/autoload.php");
}else{
    include("phar://easy.phar/autoload.php");
}
//env dev online
const ENV = 'dev';
const TEMPLATE_DIR = USER_PATH.'/templates';
const USERCONFIG_DIR = USER_PATH.'/config';
$http = new HTTP((new Gear())->init($userClasses));
$http->init($argv[1] ?? '0.0.0.0', $argv[2] ?? 8000)->start();