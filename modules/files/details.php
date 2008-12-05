<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

$period = ' AND (start = end AND start <= \'' . $date->timestamp() . '\' OR start != end AND start <= \'' . $date->timestamp() . '\' AND end >= \'' . $date->timestamp() . '\')';

if (validate::isNumber($uri->id) && $db->select('id', 'files', 'id = \'' . $uri->id . '\'' . $period, 0, 0, 0, 1) == '1') {
	$file = getFilesCache($uri->id);

	if ($uri->action == 'download') {
		$path = 'uploads/files/';
		if (is_file($path . $file[0]['file'])) {
			header('Content-Type: application/force-download');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length:' . filesize($path . $file[0]['file']));
			header('Content-Disposition: attachment; filename="' . $file[0]['file'] . '"');
			readfile($path . $file[0]['file']);
			exit;
		} else {
			redirect(0, $file[0]['file']);
		}
	} else {
		// Brotkrümelspur
		breadcrumb::assign($lang->t('files', 'files'), uri('files'));
		breadcrumb::assign($file[0]['category_name'], uri('files/files/cat_' . $file[0]['category_id']));
		breadcrumb::assign($file[0]['link_title']);

		$file[0]['size'] = !empty($file[0]['size']) ? $file[0]['size'] : $lang->t('files', 'unknown_filesize');
		$file[0]['date'] = $date->format($file[0]['start']);
		$tpl->assign('file', $file[0]);

		$content = $tpl->fetch('files/details.html');
	}
} else {
	redirect('errors/404');
}
?>