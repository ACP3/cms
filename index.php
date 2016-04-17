<?php
/**
 * Index
 *
 * @author Tino Goratsch
 */

define('ACP3_ROOT_DIR', realpath(__DIR__) . '/');

require './vendor/autoload.php';

$appMode = \ACP3\Core\Environment\ApplicationMode::PRODUCTION;
if (getenv('ACP3_APPLICATION_MODE') === \ACP3\Core\Environment\ApplicationMode::DEVELOPMENT) {
    $appMode = \ACP3\Core\Environment\ApplicationMode::DEVELOPMENT;
}

(new \ACP3\Core\Application\Bootstrap($appMode))->run();
