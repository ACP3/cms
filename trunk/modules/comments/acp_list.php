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

getRedirectMessage();

$comments = ACP3_CMS::$db->query('SELECT c.module_id, m.name AS module FROM {pre}comments AS c JOIN {pre}modules AS m ON(m.id = c.module_id) GROUP BY c.module_id LIMIT ' . POS . ',' . ACP3_CMS::$auth->entries);
$c_comments = count($comments);

if ($c_comments > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('DISTINCT module_id', 'comments')));
	for ($i = 0; $i < $c_comments; ++$i) {
		$comments[$i]['name'] = ACP3_CMS::$lang->t($comments[$i]['module'], $comments[$i]['module']);
		$comments[$i]['count'] = ACP3_CMS::$db->countRows('*', 'comments', 'module_id = ' . ((int) $comments[$i]['module_id']));
	}
	ACP3_CMS::$view->assign('comments', $comments);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('comments', 'acp_delete'));
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('comments/acp_list.tpl'));