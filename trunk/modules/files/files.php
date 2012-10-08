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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->cat) &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array(ACP3_CMS::$uri->cat)) == 1) {
	$category = ACP3_CMS::$db2->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array(ACP3_CMS::$uri->cat));

	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('files', 'files'), ACP3_CMS::$uri->route('files'))
	->append($category);

	$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
	$files = ACP3_CMS::$db2->fetchAll('SELECT id, start, file, size, title FROM ' . DB_PRE . 'files WHERE category_id = :cat_id' . $period . ' ORDER BY start DESC, end DESC, id DESC', array('cat_id' => ACP3_CMS::$uri->cat, 'time' => ACP3_CMS::$date->getCurrentDateTime()));
	$c_files = count($files);

	if ($c_files > 0) {
		$settings = ACP3_Config::getSettings('files');

		for ($i = 0; $i < $c_files; ++$i) {
			$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : ACP3_CMS::$lang->t('files', 'unknown_filesize');
			$files[$i]['date'] = ACP3_CMS::$date->format($files[$i]['start'], $settings['dateformat']);
		}
		ACP3_CMS::$view->assign('files', $files);
	}
	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('files/files.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
