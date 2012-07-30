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

if (ACP3_Validate::isNumber($uri->cat) && $db->countRows('*', 'categories', 'id = \'' . $uri->cat . '\'') == 1) {
	$category = $db->select('name', 'categories', 'id = \'' . $uri->cat . '\'');
	$breadcrumb->append($lang->t('files', 'files'), $uri->route('files'))
			   ->append($category[0]['name']);

	$time = $date->timestamp();
	$period = ' AND (start = end AND start <= ' . $time . ' OR start != end AND start <= ' . $time . ' AND end >= ' . $time . ')';

	$files = $db->select('id, start, file, size, link_title', 'files', 'category_id = \'' . $uri->cat . '\'' . $period, 'start DESC, end DESC, id DESC');
	$c_files = count($files);

	if ($c_files > 0) {
		$settings = ACP3_Config::getModuleSettings('files');

		for ($i = 0; $i < $c_files; ++$i) {
			$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $lang->t('files', 'unknown_filesize');
			$files[$i]['date'] = $date->format($files[$i]['start'], $settings['dateformat']);
			$files[$i]['link_title'] = $db->escape($files[$i]['link_title'], 3);
		}
		$tpl->assign('files', $files);
	}
	ACP3_View::setContent(ACP3_View::fetchTemplate('files/files.tpl'));
} else {
	$uri->redirect('errors/404');
}
