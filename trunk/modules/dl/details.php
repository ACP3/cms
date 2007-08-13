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

$date = ' AND (start = end AND start <= \'' . date_aligned(2, time()) . '\' OR start != end AND start <= \'' . date_aligned(2, time()) . '\' AND end >= \'' . date_aligned(2, time()) . '\')';

if (!empty($modules->id) && $db->select('id', 'dl', 'id = \'' . $modules->id . '\'' . $date, 0, 0, 0, 1) == '1') {
	if (!$cache->check('dl_details_id_' . $modules->id)) {
		$cache->create('dl_details_id_' . $modules->id, $db->select('d.id, d.start, d.file, d.size, d.link_title, d.text, c.id AS cat_id, c.name AS cat_name', 'dl AS d, ' . CONFIG_DB_PRE . 'categories AS c', 'd.id = \'' . $modules->id . '\' AND d.cat = c.id'));
	}
	$file = $cache->output('dl_details_id_' . $modules->id);

	if (isset($modules->gen['download']) && $modules->gen['download'] == '1') {
		$path = 'files/dl/';
		if (is_file($path . $file[0]['file'])) {
			$file_new = $path . $file[0]['file'];
			header('Content-Type: application/force-download');
			header('Content-Transfer-Encoding: binary');
			header('Content-Disposition: attachment; filename="' . $file[0]['file'] . '"');
			readfile($file_new);
			exit;
		} else {
			redirect(0, $file[0]['file']);
		}
	} else {
		// Brotkrümelspur
		$breadcrumb->assign(lang('dl', 'dl'), uri('dl'));
		$breadcrumb->assign($file[0]['cat_name'], uri('dl/files/cat_' . $file[0]['cat_id']));
		$breadcrumb->assign($file[0]['link_title']);

		$file[0]['size'] = file_exists('files/dl/' . $file[0]['file']) ? $file[0]['size'] . ' MB' : lang('dl', 'unknown_filesize');
		$file[0]['date'] = date_aligned(1, $file[0]['start']);
		$tpl->assign('file', $file[0]);

		$content = $tpl->fetch('dl/details.html');
	}
} else {
	redirect('errors/404');
}
?>