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
require ACP3_ROOT_DIR . 'ACP3/CMS.php';

\ACP3\CMS::run();