<?php
/**
 * Index
 *
 * @author Tino Goratsch
 */

define('IN_ACP3', true);
define('ACP3_ROOT_DIR', realpath(__DIR__) . '/');

require './vendor/autoload.php';

$application = new \ACP3\Application();
$application->run();
