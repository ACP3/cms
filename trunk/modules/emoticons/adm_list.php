<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$emoticons = $db->select('id, code, description, img', 'emoticons', 0, 'id DESC', POS, $auth->entries);

if (count($emoticons) > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'emoticons')));
	$tpl->assign('emoticons', $emoticons);
}
$content = modules::fetchTemplate('emoticons/adm_list.html');
