<?php
printf("require [%s]\n", dirname(__FILE__) . '/../vendor/autoload.php');
$loader = require_once dirname(__FILE__) . '/../vendor/autoload.php';
$loader->add('CA_Config', __DIR__);
