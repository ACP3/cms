<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Installer
 */

define('IN_ACP3', true);
define('IN_INSTALL', true);
define('IN_UPDATER', true);

define('ACP3_ROOT_DIR', realpath(__DIR__ . '/../') . '/');
require ACP3_ROOT_DIR . 'ACP3/Installer/Installer.php';

\ACP3\Installer\Installer::runInstaller();