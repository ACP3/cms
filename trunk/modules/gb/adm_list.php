<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check())
	redirect('errors/403');
if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/gb/entry.php';
} else {
	$gb = $db->select('id, ip, date, name, message', 'gb', 0, 'date DESC', POS, CONFIG_ENTRIES);
	$c_gb = count($gb);

	if ($c_gb > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'gb', 0, 0, 0, 0, 1)));
		$emoticons = false;

		// Emoticons einbinden
		if ($modules->check(1, 'emoticons', 'info')) {
			include_once 'modules/emoticons/functions.php';
			$emoticons = true;
		}

		for ($i = 0; $i < $c_gb; $i++) {
			$gb[$i]['date'] = date_aligned(1, $gb[$i]['date']);
			$gb[$i]['name'] = $gb[$i]['name'];
			$gb[$i]['message'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $gb[$i]['message']);
			if ($emoticons) {
				$gb[$i]['message'] = emoticons_replace($gb[$i]['message']);
			}
		}
		$tpl->assign('gb', $gb);
	}
	$content = $tpl->fetch('gb/adm_list.html');
}
?>