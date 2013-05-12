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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->cat) &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array(ACP3\CMS::$injector['URI']->cat)) == 1) {
	$category = ACP3\CMS::$injector['Db']->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array(ACP3\CMS::$injector['URI']->cat));

	ACP3\CMS::$injector['Breadcrumb']
	->append(ACP3\CMS::$injector['Lang']->t('files', 'files'), ACP3\CMS::$injector['URI']->route('files'))
	->append($category);

	$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
	$files = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, file, size, title FROM ' . DB_PRE . 'files WHERE category_id = :cat_id' . $period . ' ORDER BY start DESC, end DESC, id DESC', array('cat_id' => ACP3\CMS::$injector['URI']->cat, 'time' => ACP3\CMS::$injector['Date']->getCurrentDateTime()));
	$c_files = count($files);

	if ($c_files > 0) {
		$settings = ACP3\Core\Config::getSettings('files');

		for ($i = 0; $i < $c_files; ++$i) {
			$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : ACP3\CMS::$injector['Lang']->t('files', 'unknown_filesize');
			$files[$i]['date_formatted'] = ACP3\CMS::$injector['Date']->format($files[$i]['start'], $settings['dateformat']);
			$files[$i]['date_iso'] = ACP3\CMS::$injector['Date']->format($files[$i]['start'], 'c');
		}
		ACP3\CMS::$injector['View']->assign('files', $files);
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
