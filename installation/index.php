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

define('ACP3_ROOT', realpath(__DIR__ . '/../') . '/');
require ACP3_ROOT . 'installation/includes/common.php';

$tpl->display('layout.tpl');