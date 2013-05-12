<?php
/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

ACP3\Core\Functions::getRedirectMessage();

$comments = ACP3\CMS::$injector['Db']->fetchAll('SELECT c.module_id, m.name AS module, COUNT(c.module_id) AS `comments_count` FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) GROUP BY c.module_id ORDER BY m.name');
$c_comments = count($comments);

if ($c_comments > 0) {
	$can_delete = ACP3\Core\Modules::check('comments', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 1 : 0,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));
	for ($i = 0; $i < $c_comments; ++$i) {
		$comments[$i]['name'] = ACP3\CMS::$injector['Lang']->t($comments[$i]['module'], $comments[$i]['module']);
	}
	ACP3\CMS::$injector['View']->assign('comments', $comments);
	ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
}