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

ACP3\CMS::$injector['Breadcrumb']->append(ACP3\CMS::$injector['Lang']->t('newsletter', 'archive'));

if (isset($_POST['newsletter']) === true &&
	ACP3\Core\Validate::isNumber($_POST['newsletter'])) {
	$id = (int) $_POST['newsletter'];

	$newsletter = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT date, title, text FROM ' . DB_PRE . 'newsletters WHERE id = ? AND status = ?', array($id, 1));
	if (!empty($newsletter)) {
		$newsletter['date_formatted'] = ACP3\CMS::$injector['Date']->format($newsletter['date'], 'short');
		$newsletter['date_iso'] = ACP3\CMS::$injector['Date']->format($newsletter['date'], 'c');
		$newsletter['text'] = ACP3\Core\Functions::nl2p($newsletter['text']);

		ACP3\CMS::$injector['View']->assign('newsletter', $newsletter);
	}
}

$newsletters = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, date, title FROM ' . DB_PRE . 'newsletters WHERE status = ? ORDER BY date DESC', array(1));
$c_newsletters = count($newsletters);

if ($c_newsletters > 0) {
	for ($i = 0; $i < $c_newsletters; ++$i) {
		$newsletters[$i]['date_formatted'] = ACP3\CMS::$injector['Date']->format($newsletters[$i]['date'], 'short');
		$newsletters[$i]['selected'] = ACP3\Core\Functions::selectEntry('newsletter', $newsletters[$i]['id']);
	}
	ACP3\CMS::$injector['View']->assign('newsletters', $newsletters);
}