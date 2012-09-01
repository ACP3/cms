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

$comments = $db->query('SELECT c.module_id, m.name AS module FROM {pre}comments AS c JOIN {pre}modules AS m ON(m.id = c.module_id) GROUP BY c.module_id LIMIT ' . POS . ',' . $auth->entries);
$c_comments = count($comments);

if ($c_comments > 0) {
	$tpl->assign('pagination', pagination($db->countRows('DISTINCT module_id', 'comments')));
	for ($i = 0; $i < $c_comments; ++$i) {
		$comments[$i]['name'] = $lang->t($comments[$i]['module'], $comments[$i]['module']);
		$comments[$i]['count'] = $db->countRows('*', 'comments', 'module_id = ' . ((int) $comments[$i]['module_id']));
	}
	$tpl->assign('comments', $comments);
	$tpl->assign('can_delete', ACP3_Modules::check('comments', 'acp_delete'));
}

ACP3_View::setContent(ACP3_View::fetchTemplate('comments/acp_list.tpl'));