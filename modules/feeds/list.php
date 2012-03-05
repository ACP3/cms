<?php
if (defined('IN_ACP3') === false)
	exit;

if (ACP3_Modules::check($uri->feed, 'extensions/feeds') === true) {
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
	ACP3_View::setContentType('Content-Type: application/xml; charset="UTF-8"');
	ACP3_View::assignLayout('feeds/rss.tpl');
} else {
	$uri->redirect(0, ROOT_DIR);
}