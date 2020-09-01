<?php
$phar = new Phar("gear2.phar");
$phar->setDefaultStub('main.php');
$phar->buildFromDirectory(dirname(__FILE__).'/gear');

$phar = new Phar("gear2new.phar");
$phar->setDefaultStub('main2.php');
$phar->buildFromDirectory(dirname(__FILE__).'/gear');