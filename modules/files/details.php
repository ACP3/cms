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

$time = ACP3_CMS::$date->getCurrentDateTime();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'files', 'id = \'' . ACP3_CMS::$uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'files/functions.php';

	$file = getFilesCache(ACP3_CMS::$uri->id);

	if (ACP3_CMS::$uri->action === 'download') {
		$path = 'uploads/files/';
		if (is_file($path . $file[0]['file'])) {
			// Schönen Dateinamen generieren
			$ext = strrchr($file[0]['file'], '.');
			$filename = makeStringUrlSafe(ACP3_CMS::$db->escape($file[0]['link_title'], 3)) . $ext;

			header('Content-Type: application/force-download');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length:' . filesize($path . $file[0]['file']));
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			readfile($path . $file[0]['file']);
			exit;
		} elseif (preg_match('/^([a-z]+):\/\//', $file[0]['file'])) {
			ACP3_CMS::$uri->redirect(0, $file[0]['file']);
		} else {
		    ACP3_CMS::$uri->redirect('errors/404');
		}
	} else {
		$file[0]['link_title'] = ACP3_CMS::$db->escape($file[0]['link_title'], 3);
		$file[0]['text'] = ACP3_CMS::$db->escape($file[0]['text'], 3);

		// Brotkrümelspur
		ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('files', 'files'), ACP3_CMS::$uri->route('files'))
				   ->append($file[0]['category_name'], ACP3_CMS::$uri->route('files/files/cat_' . $file[0]['category_id']))
				   ->append($file[0]['link_title']);

		$settings = ACP3_Config::getSettings('files');

		$file[0]['size'] = !empty($file[0]['size']) ? $file[0]['size'] : ACP3_CMS::$lang->t('files', 'unknown_filesize');
		$file[0]['date'] = ACP3_CMS::$date->format($file[0]['start'], $settings['dateformat']);
		ACP3_CMS::$view->assign('file', $file[0]);

		if ($settings['comments'] == 1 && $file[0]['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
			require_once MODULES_DIR . 'comments/functions.php';

			ACP3_CMS::$view->assign('comments', commentsList('files', ACP3_CMS::$uri->id));
		}
		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('files/details.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}