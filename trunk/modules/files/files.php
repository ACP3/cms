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

if (validate::isNumber($uri->cat) && $db->select('id', 'categories', 'id = \'' . $uri->cat . '\'', 0, 0, 0, 1) == '1') {
	breadcrumb::assign($lang->t('files', 'files'), uri('files'));
	$category = $db->select('name', 'categories', 'id = \'' . $uri->cat . '\'');
	breadcrumb::assign($category[0]['name']);
	$period = ' AND (start = end AND start <= \'' . $date->timestamp() . '\' OR start != end AND start <= \'' . $date->timestamp() . '\' AND end >= \'' . $date->timestamp() . '\')';

	$files = $db->select('id, start, file, size, link_title', 'files', 'category_id = \'' . $uri->cat . '\'' . $period, 'start DESC, end DESC');
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