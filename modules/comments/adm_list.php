<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$module = isset($modules->gen['module']) && !empty($modules->gen['module']) ? $db->escape($modules->gen['module']) : 0;
$tpl->assign('module', $module);

if (empty($module) || !empty($module) && $db->select('id', 'comments', 'module = \'' . $module . '\'', 0, 0, 0, 1) == '0') {
	$comments = $db->query('SELECT module FROM ' . CONFIG_DB_PRE . 'comments GROUP BY module LIMIT ' . POS . ',' . CONFIG_ENTRIES);
	$c_comments = count($comments);

	if ($c_comments > 0) {
		$tpl->assign('pagination', pagination($db->query('SELECT module FROM ' . CONFIG_DB_PRE . 'comments GROUP BY module', 1)));
		for ($i = 0; $i < $c_comments; $i++) {
			$comments[$i]['name'] = lang($comments[$i]['module'], $comments[$i]['module']);
			$comments[$i]['count'] = $db->select('id', 'comments', 'module = \'' . $comments[$i]['module'] . '\'', 0, 0, 0, 1);
		}
		$tpl->assign('comments', $comments);
	}
} elseif (!empty($module) && $db->select('id', 'comments', 'module = \'' . $module . '\'', 0, 0, 0, 1) > '0') {
	//Brotkrümelspur
	$breadcrumb->assign(lang('common', 'acp'), uri('acp'));
	$breadcrumb->assign(lang('comments', 'comments'), uri('acp/comments'));
	$breadcrumb->assign(lang($module, $module));

	$comments = $db->select('id, ip, date, name, message', 'comments', 'module = \'' . $module . '\'', 'date DESC', POS, CONFIG_ENTRIES);
	$c_comments = count($comments);
	$emoticons = false;

	// Emoticons einbinden
	if ($modules->check('emoticons', 'functions')) {
		include_once ACP3_ROOT . 'modules/emoticons/functions.php';
		$emoticons = true;
	}

	if ($c_comments > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'comments', 'module = \'' . $module . '\'', 0, 0, 0, 1)));
		for ($i = 0; $i < $c_comments; $i++) {
			$comments[$i]['date'] = date_aligned(1, $comments[$i]['date']);
			$comments[$i]['name'] = $comments[$i]['name'];
			$comments[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $comments[$i]['message']);
			if ($emoticons) {
				$comments[$i]['message'] = emoticons_replace($comments[$i]['message']);
			}
		}
		$tpl->assign('comments', $comments);
	}
}
$content = $tpl->fetch('comments/adm_list.html');
?>