<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/emoticons/entry.php';
} else {
	$emoticons = $db->select('id, code, description, img', 'emoticons', 0, 'id DESC', POS, CONFIG_ENTRIES);
	$c_emoticons = count($emoticons);

	if ($c_emoticons > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'emoticons', 0, 0, 0, 0, 1)));
		for ($i = 0; $i < $c_emoticons; $i++) {
			$emoticons[$i]['code'] = $emoticons[$i]['code'];
			$emoticons[$i]['description'] = $emoticons[$i]['description'];
		}
		$tpl->assign('emoticons', $emoticons);
	}
	$content = $tpl->fetch('emoticons/acp_list.html');
}
?>