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

$date = ' AND (start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')';

if (!empty($modules->id) && $db->select('id', 'files', 'id = \'' . $modules->id . '\'' . $date, 0, 0, 0, 1) == '1') {
	if (!$cache->check('files_details_id_' . $modules->id)) {
		$cache->create('files_details_id_' . $modules->id, $db->select('f.id, f.start, f.category_id, f.file, f.size, f.link_title, f.text, c.name AS category_name', 'files AS f, ' . CONFIG_DB_PRE . 'categories AS c', 'f.id = \'' . $modules->id . '\' AND f.category_id = c.id'));
	}
	$file = $cache->output('files_details_id_' . $modules->id);

	if ($modules->action == 'download') {
		$path = 'uploads/files/';
		if (is_file($path . $file[0]['file'])) {
			header('Content-Type: application/force-download');
			header('Content-Transfer-Encoding: binary');
			header('Content-Disposition: attachment; filename="' . $file[0]['file'] . '"');
			readfile($path . $file[0]['file']);
			exit;
		} else {
			redirect(0, $file[0]['file']);
		}
	} else {
		// Brotkrümelspur
		$breadcrumb->assign(lang('files', 'files'), uri('files'));
		$breadcrumb->assign($file[0]['category_name'], uri('files/files/cat_' . $file[0]['category_id']));
		$breadcrumb->assign($file[0]['link_title']);

		$file[0]['size'] = !empty($file[0]['size']) ? $file[0]['size'] : lang('files', 'unknown_filesize');
		$file[0]['date'] = dateAligned(1, $file[0]['start']);
		$tpl->assign('file', $file[0]);

		$content = $tpl->fetch('files/details.html');
	}
} else {
	redirect('errors/404');
}
?>