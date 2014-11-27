<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 */

define('IN_ACP3', true);
define('IN_INSTALL', true);
define('IN_UPDATER', true);

define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../') . '/');
require ACP3_ROOT_DIR . 'installation/Installer/Application.php';

$application = new \ACP3\Installer\Application();
$application->runUpdater();
