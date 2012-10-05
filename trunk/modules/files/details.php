<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$period = ' AND (start = end AND start <= :time OR :time BETWEEN start AND end)';

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'files WHERE id = :id' . $period, array('id' => ACP3_CMS::$uri->id, 'time' => ACP3_CMS::$date->getCurrentDateTime())) == 1) {
	require_once MODULES_DIR . 'files/functions.php';

	$file = getFilesCache(ACP3_CMS::$uri->id);

	if (ACP3_CMS::$uri->action === 'download') {
		$path = UPLOADS_DIR . 'files/';
		if (is_file($path . $file['file'])) {
			// Schönen Dateinamen generieren
			$ext = strrchr($file['file'], '.');
			$filename = makeStringUrlSafe($file['title']) . $ext;

			header('Content-Type: application/force-download');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length:' . filesize($path . $file['file']));
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			readfile($path . $file['file']);
			exit;
		// Externe Datei
		} elseif (preg_match('/^([a-z]+):\/\//', $file['file'])) {
			ACP3_CMS::$uri->redirect(0, $file['file']);
		} else {
		    ACP3_CMS::$uri->redirect('errors/404');
		}
	} else {
		// Brotkrümelspur
		ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('files', 'files'), ACP3_CMS::$uri->route('files'))
				   ->append($file['category_name'], ACP3_CMS::$uri->route('files/files/cat_' . $file['category_id']))
				   ->append($file['title']);

		$settings = ACP3_Config::getSettings('files');

		$file['size'] = !empty($file['size']) ? $file['size'] : ACP3_CMS::$lang->t('files', 'unknown_filesize');
		$file['date'] = ACP3_CMS::$date->format($file['start'], $settings['dateformat']);
		ACP3_CMS::$view->assign('file', $file);

		if ($settings['comments'] == 1 && $file['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
			require_once MODULES_DIR . 'comments/functions.php';

			ACP3_CMS::$view->assign('comments', commentsList('files', ACP3_CMS::$uri->id));
		}
		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('files/details.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}