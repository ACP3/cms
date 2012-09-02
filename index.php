<?php
/**
 * Index
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

define('IN_ACP3', true);
define('ACP3_ROOT', realpath(__DIR__) . '/');
require ACP3_ROOT . 'includes/bootstrap.php';

ACP3_CMS::run();