<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

require_once ACP3_ROOT . 'modules/menu_items/functions.php';

$pages_list = pagesList();

if (count($pages_list) > 0) {
	$tpl->assign('pages_list', $pages_list);
}
$content = modules::fetchTemplate('menu_items/adm_list.html');
