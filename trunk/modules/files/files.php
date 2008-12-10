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

if (validate::isNumber($uri->cat) && $db->select('COUNT(id)', 'categories', 'id = \'' . $uri->cat . '\'', 0, 0, 0, 1) == '1') {
	breadcrumb::assign($lang->t('files', 'files'), uri('files'));
	$category = $db->select('name', 'categories', 'id = \'' . $uri->cat . '\'');
	breadcrumb::assign($category[0]['name']);

	$time = $date->timestamp();
	$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

	$files = $db->select('id, start, file, size, link_title', 'files', 'category_id = \'' . $uri->cat . '\'' . $period, 'start DESC, end DESC, id DESC');
	$c_files = count($files);

	if ($c_files > 0) {
		for ($i = 0; $i < $c_files; ++$i) {
			$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $lang->t('files', 'unknown_filesize');
			$files[$i]['date'] = $date->format($files[$i]['start']);
		}
		$tpl->assign('files', $files);
	}
	$content = $tpl->fetch('files/files.html');
} else {
	redirect('errors/404');
}
?>