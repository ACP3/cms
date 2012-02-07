<?php
if (defined('IN_ACP3') === false)
	exit;

if (modules::check($uri->feed, 'extensions/feeds') === true) {
	$module = $uri->feed;

	$link = 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES);

	//RSS Kopf Felder
	$feed = array(
		'generator' => CONFIG_VERSION,
		'atom_link' => $link . $uri->route($uri->mod . '/' . $uri->file . '/feed_' . $module),
		'link' => $link . ROOT_DIR,
		'description' => $lang->t($module, $module),
	);

	$tpl->assign('feed', $feed);

	//Einträge einbinden
	include MODULES_DIR . $module . '/extensions/feeds.php';

	// Content-Type setzen und Layout für den RSS-Feed laden
	view::setContentType('Content-Type: application/xml; charset="UTF-8"');
	view::assignLayout('feeds/rss.tpl');
} else {
	$uri->redirect(0, ROOT_DIR);
}