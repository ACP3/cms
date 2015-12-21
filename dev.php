<?php
/**
 * Debug
 *
 * @author Tino Goratsch
 */

define('ACP3_ROOT_DIR', realpath(__DIR__) . '/');

require './vendor/autoload.php';

(new \ACP3\Core\Application\Bootstrap(\ACP3\Core\Environment\ApplicationMode::DEVELOPMENT))->run();