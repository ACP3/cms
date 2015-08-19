<?php
/**
 * Debug
 *
 * @author Tino Goratsch
 */

define('IN_ACP3', true);
define('ACP3_ROOT_DIR', realpath(__DIR__) . '/');

require './vendor/autoload.php';

(new \ACP3\Core\Application(\ACP3\Core\Enum\Environment::DEVELOPMENT))->run();