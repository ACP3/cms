<?php
/**
 * Categories
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$categories = $db->select('id, name, description, module', 'categories', 0, 'module ASC, name DESC, id DESC', POS, $auth->entries);
$c_categories = count($categories);

if ($c_categories > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'categories')));
	for ($i = 0; $i < $c_categories; ++$i) {
		$categories[$i]['name'] = $db->escape($categories[$i]['name'], 3);
		$categories[$i]['description'] = $db->escape($categories[$i]['description'], 3);
		$info = ACP3_Modules::parseInfo($db->escape($categories[$i]['module'], 3));
		$categories[$i]['module'] = $info['name'];
	}
	$tpl->assign('categories', $categories);
	$tpl->assign('can_delete', ACP3_Modules::check('categories', 'acp_delete'));
}
ACP3_View::setContent(ACP3_View::fetchTemplate('categories/acp_list.tpl'));
