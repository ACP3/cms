<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit();

ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('newsletter', 'archive'));

if (isset($_POST['newsletter']) === true &&
	ACP3_Validate::isNumber($_POST['newsletter'])) {
	$id = (int) $_POST['newsletter'];

	$newsletter = ACP3_CMS::$db2->fetchAssoc('SELECT date, title, text FROM ' . DB_PRE . 'newsletters WHERE id = ? AND status = ?', array($id, 1));
	if (!empty($newsletter)) {
		$newsletter['date_formatted'] = ACP3_CMS::$date->format($newsletter['date'], 'short');
		$newsletter['text'] = nl2p($newsletter['text']);

		ACP3_CMS::$view->assign('newsletter', $newsletter);
	}
}

$newsletters = ACP3_CMS::$db2->fetchAll('SELECT id, date, title FROM ' . DB_PRE . 'newsletters WHERE status = ?', array(1));
$c_newsletters = count($newsletters);

if ($c_newsletters > 0) {
	for ($i = 0; $i < $c_newsletters; ++$i) {
		$newsletters[$i]['date_formatted'] = ACP3_CMS::$date->format($newsletters[$i]['date'], 'short');
		$newsletters[$i]['selected'] = selectEntry('newsletter', $newsletters[$i]['id']);
	}
	ACP3_CMS::$view->assign('newsletters', $newsletters);
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('newsletter/archive.tpl'));