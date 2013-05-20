<?php
/**
 * Index
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

define('IN_ACP3', true);
define('ACP3_ROOT_DIR', realpath(__DIR__) . '/');
require ACP3_ROOT_DIR . 'ACP3/Application.php';

\ACP3\Application::run();