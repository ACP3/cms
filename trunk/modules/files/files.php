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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->cat) && ACP3_CMS::$db->countRows('*', 'categories', 'id = \'' . ACP3_CMS::$uri->cat . '\'') == 1) {
	$category = ACP3_CMS::$db->select('name', 'categories', 'id = \'' . ACP3_CMS::$uri->cat . '\'');
	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('files', 'files'), ACP3_CMS::$uri->route('files'))
			   ->append($category[0]['name']);

	$time = ACP3_CMS::$date->getCurrentDateTime();
	$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

	$files = ACP3_CMS::$db->select('id, start, file, size, link_title', 'files', 'category_id = \'' . ACP3_CMS::$uri->cat . '\'' . $period, 'start DESC, end DESC, id DESC');
	$c_files = count($files);

	if ($c_files > 0) {
		$settings = ACP3_Config::getSettings('files');

		for ($i = 0; $i < $c_files; ++$i) {
			$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : ACP3_CMS::$lang->t('files', 'unknown_filesize');
			$files[$i]['date'] = ACP3_CMS::$date->format($files[$i]['start'], $settings['dateformat']);
			$files[$i]['link_title'] = ACP3_CMS::$db->escape($files[$i]['link_title'], 3);
		}
		ACP3_CMS::$view->assign('files', $files);
	}
	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('files/files.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
