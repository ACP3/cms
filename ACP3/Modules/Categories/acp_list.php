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

ACP3\Core\Functions::getRedirectMessage();

$categories = ACP3\CMS::$injector['Db']->fetchAll('SELECT c.id, c.title, c.description, m.name AS module FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) ORDER BY m.name ASC, c.title DESC, c.id DESC');
$c_categories = count($categories);

if ($c_categories > 0) {
	$can_delete = ACP3\Core\Modules::check('categories', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 1 : 0,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));
	for ($i = 0; $i < $c_categories; ++$i) {
		$categories[$i]['module'] = ACP3\CMS::$injector['Lang']->t($categories[$i]['module'], $categories[$i]['module']);
	}
	ACP3\CMS::$injector['View']->assign('categories', $categories);
	ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
}