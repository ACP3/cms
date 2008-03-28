<?php
if (!defined('IN_ACP3'))
	exit;

if ($modules->feed) {
	$module = $modules->feed;

	$link = 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES);

	//RSS Kopf Felder
	$feed['generator'] = CONFIG_VERSION;
	$feed['atom_link'] = $link . uri($modules->mod . '/' . $modules->page . '/feed_' . $module);
	$feed['link'] = $link . ROOT_DIR;
	$feed['description'] = lang($module, $module);

	$tpl->assign('feed', $feed);

	//Einträge einbinden
	if (isset($module) && $modules->check($module, 'extensions/feeds')) {
		include ACP3_ROOT . 'modules/' . $module . '/extensions/feeds.php';
	}

	// Content-Type setzen und Layout für den RSS-Feed laden
	define('CUSTOM_CONTENT_TYPE', 'application/xml');
	define('CUSTOM_LAYOUT', 'feeds/rss.html');
} else {
	redirect(0, ROOT_DIR);
}
?>