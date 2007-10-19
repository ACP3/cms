<?php
if (!defined('IN_FRONTEND'))
	exit;

if (isset($modules->gen['feed'])) {
	$module = $modules->gen['feed'];

	$link = 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES);

	//RSS Kopf Felder
	$rss['link'] = $link . ROOT_DIR;
	$rss['description'] = lang($module, $module);

	$tpl->assign('rss', $rss);

	//Einträge einbinden
	if (isset($module) && $modules->check($module, 'extensions/feeds')) {
		include 'modules/' . $module . '/extensions/feeds.php';
	}

	// Content-Type setzen und Layout für den RSS-Feed laden
	define('CUSTOM_CONTENT_TYPE', 'application/xml');
	define('CUSTOM_LAYOUT', 'feeds/rss.html');
} else {
	redirect(0, ROOT_DIR);
}
?>