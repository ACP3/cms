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
$c_emoticons = count($emoticons);

if ($c_emoticons > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'emoticons')));
	for ($i = 0; $i < $c_emoticons; ++$i) {
		$emoticons[$i]['code'] = $emoticons[$i]['code'];
		$emoticons[$i]['description'] = $emoticons[$i]['description'];
	}
	$tpl->assign('emoticons', $emoticons);
}
$content = modules::fetchTemplate('emoticons/adm_list.html');
