<?php
if (defined('IN_ACP3') === false)
	exit;

if (modules::check($uri->feed, 'extensions/feeds') == 1) {
	$module = $uri->feed;

	$link = 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES);

	//RSS Kopf Felder
	$feed['generator'] = CONFIG_VERSION;
	$feed['atom_link'] = $link . $uri->route($uri->mod . '/' . $uri->file . '/feed_' . $module);
	$feed['link'] = $link . ROOT_DIR;
	$feed['description'] = $lang->t($module, $module);

	$tpl->assign('feed', $feed);

	//Einträge einbinden
	include MODULES_DIR . '' . $module . '/extensions/feeds.php';

	// Content-Type setzen und Layout für den RSS-Feed laden
	define('CUSTOM_CONTENT_TYPE', 'application/xml');
	define('CUSTOM_LAYOUT', 'feeds/rss.html');
} else {
	$uri->redirect(0, ROOT_DIR);
}
