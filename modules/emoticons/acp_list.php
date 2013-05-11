<?php
/**
 * Emoticons
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$emoticons = ACP3_CMS::$db2->fetchAll('SELECT id, code, description, img FROM ' . DB_PRE . 'emoticons ORDER BY id DESC');
$c_emoticons = count($emoticons);

if ($c_emoticons > 0) {
	$can_delete = ACP3_Modules::check('emoticons', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 4 : 3,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::$view->appendContent(datatable($config));
	ACP3_CMS::$view->assign('emoticons', $emoticons);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
}