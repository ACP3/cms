<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$emoticons = $db->select('id, code, description, img', 'emoticons', 0, 'id DESC', POS, $session->get('entries'));
$c_emoticons = count($emoticons);

if ($c_emoticons > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'emoticons')));
	for ($i = 0; $i < $c_emoticons; ++$i) {
		$emoticons[$i]['code'] = $db->escape($emoticons[$i]['code'], 3);
		$emoticons[$i]['description'] = $db->escape($emoticons[$i]['description'], 3);
	}
	$tpl->assign('emoticons', $emoticons);
}
view::setContent(view::fetchTemplate('emoticons/adm_list.tpl'));
