<?php
/**
 * Index
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
define('IN_ACP3', true);
define('ACP3_ROOT', realpath(dirname(__FILE__)) . '/');
require ACP3_ROOT . 'includes/common.php';

ACP3_View::outputPage();