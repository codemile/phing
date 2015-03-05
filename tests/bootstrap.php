<?php

/**
 * This is the bootstrap for running phpunit.
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Europe/London');

$loader = require(dirname(__DIR__).'/vendor/autoload.php');
$loader->add('GemsPhing', dirname(__DIR__).'/src');
