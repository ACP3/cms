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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'files WHERE id = :id' . $period, array('id' => ACP3\CMS::$injector['URI']->id, 'time' => ACP3\CMS::$injector['Date']->getCurrentDateTime())) == 1) {
	require_once MODULES_DIR . 'files/functions.php';

	$file = getFilesCache(ACP3\CMS::$injector['URI']->id);

	if (ACP3\CMS::$injector['URI']->action === 'download') {
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
			ACP3\CMS::$injector['URI']->redirect(0, $file['file']);
		} else {
		    ACP3\CMS::$injector['URI']->redirect('errors/404');
		}
	} else {
		// Brotkrümelspur
		ACP3\CMS::$injector['Breadcrumb']->append(ACP3\CMS::$injector['Lang']->t('files', 'files'), ACP3\CMS::$injector['URI']->route('files'))
				   ->append($file['category_name'], ACP3\CMS::$injector['URI']->route('files/files/cat_' . $file['category_id']))
				   ->append($file['title']);

		$settings = ACP3\Core\Config::getSettings('files');

		$file['size'] = !empty($file['size']) ? $file['size'] : ACP3\CMS::$injector['Lang']->t('files', 'unknown_filesize');
		$file['date_formatted'] = ACP3\CMS::$injector['Date']->format($file['start'], $settings['dateformat']);
		$file['date_iso'] = ACP3\CMS::$injector['Date']->format($file['start'], 'c');
		ACP3\CMS::$injector['View']->assign('file', $file);

		if ($settings['comments'] == 1 && $file['comments'] == 1 && ACP3\Core\Modules::check('comments', 'functions') === true) {
			require_once MODULES_DIR . 'comments/functions.php';

			ACP3\CMS::$injector['View']->assign('comments', commentsList('files', ACP3\CMS::$injector['URI']->id));
		}
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}