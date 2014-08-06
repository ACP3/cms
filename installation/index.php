<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 */

define('IN_ACP3', true);
define('IN_INSTALL', true);

define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../') . '/');
require ACP3_ROOT_DIR . 'ACP3/Installer/Application.php';

$application = new \ACP3\Installer\Application();
$application->runInstaller();