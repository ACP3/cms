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

$categories = ACP3_CMS::$db->query('SELECT c.id, c.name, c.description, m.name AS module FROM {pre}categories AS c JOIN {pre}modules AS m ON(m.id = c.module_id) ORDER BY m.name ASC, c.name DESC, c.id DESC LIMIT ' . POS . ',' . ACP3_CMS::$auth->entries);
$c_categories = count($categories);

if ($c_categories > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'categories')));
	for ($i = 0; $i < $c_categories; ++$i) {
		$categories[$i]['name'] = ACP3_CMS::$db->escape($categories[$i]['name'], 3);
		$categories[$i]['description'] = ACP3_CMS::$db->escape($categories[$i]['description'], 3);
		$info = ACP3_Modules::getModuleInfo(ACP3_CMS::$db->escape($categories[$i]['module'], 3));
		$categories[$i]['module'] = $info['name'];
	}
	ACP3_CMS::$view->assign('categories', $categories);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('categories', 'acp_delete'));
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('categories/acp_list.tpl'));
