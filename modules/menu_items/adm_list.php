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

$pages_list = pagesList();

if (count($pages_list) > 0) {
	$tpl->assign('pages_list', $pages_list);
}
$content = $tpl->fetch('menu_items/adm_list.html');
?>