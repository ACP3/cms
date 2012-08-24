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

$time = $date->getCurrentDateTime();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'files', 'id = \'' . $uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'files/functions.php';

	$file = getFilesCache($uri->id);

	if ($uri->action === 'download') {
		$path = 'uploads/files/';
		if (is_file($path . $file[0]['file'])) {
			// Schönen Dateinamen generieren
			$ext = strrchr($file[0]['file'], '.');
			$filename = makeStringUrlSafe($db->escape($file[0]['link_title'], 3)) . $ext;

			header('Content-Type: application/force-download');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length:' . filesize($path . $file[0]['file']));
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			readfile($path . $file[0]['file']);
			exit;
		} elseif (preg_match('/^([a-z]+):\/\//', $file[0]['file'])) {
			$uri->redirect(0, $file[0]['file']);
		} else {
		    $uri->redirect('errors/404');
		}
	} else {
		$file[0]['link_title'] = $db->escape($file[0]['link_title'], 3);
		$file[0]['text'] = $db->escape($file[0]['text'], 3);

		// Brotkrümelspur
		$breadcrumb->append($lang->t('files', 'files'), $uri->route('files'))
				   ->append($file[0]['category_name'], $uri->route('files/files/cat_' . $file[0]['category_id']))
				   ->append($file[0]['link_title']);

		$settings = ACP3_Config::getSettings('files');

		$file[0]['size'] = !empty($file[0]['size']) ? $file[0]['size'] : $lang->t('files', 'unknown_filesize');
		$file[0]['date'] = $date->format($file[0]['start'], $settings['dateformat']);
		$tpl->assign('file', $file[0]);

		if ($settings['comments'] == 1 && $file[0]['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
			require_once MODULES_DIR . 'comments/functions.php';

			$tpl->assign('comments', commentsList('files', $uri->id));
		}
		ACP3_View::setContent(ACP3_View::fetchTemplate('files/details.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}