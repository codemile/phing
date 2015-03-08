<?php

/**
 * This is the bootstrap for running phpunit.
 */

error_reporting(E_ALL | E_STRICT);

$loader = require(dirname(__DIR__).'/vendor/autoload.php');
$loader->add('GemsPhing', dirname(__DIR__).'/src');
$loader->add('GemsPhingTest', dirname(__DIR__).'/tests');
