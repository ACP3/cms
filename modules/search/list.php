<?php
/**
 * Search
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (strlen($_POST['search_term']) < 3)
		$errors['search-term'] = ACP3_CMS::$lang->t('search', 'search_term_to_short');
	if (empty($_POST['mods']))
		$errors[] = ACP3_CMS::$lang->t('search', 'no_module_selected');
	if (empty($_POST['area']))
		$errors[] = ACP3_CMS::$lang->t('search', 'no_area_selected');
	if (empty($_POST['sort']) || $_POST['sort'] != 'asc' && $_POST['sort'] != 'desc')
		$errors[] = ACP3_CMS::$lang->t('search', 'no_sorting_selected');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} else {
		ACP3_CMS::$breadcrumb
		->append(ACP3_CMS::$lang->t('search', 'search'), ACP3_CMS::$uri->route('search'))
		->append(ACP3_CMS::$lang->t('search', 'search_results'));

		$_POST['search_term'] = str_encode($_POST['search_term']);
		$_POST['sort'] = strtoupper($_POST['sort']);
		$results = array();
		foreach ($_POST['mods'] as $module) {
			if (ACP3_Modules::check($module, 'extensions/search') === true) {
				include MODULES_DIR . $module . '/extensions/search.php';
			}
		}
		if (!empty($results)) {
			ksort($results);
			ACP3_CMS::$view->assign('results_mods', $results);
		} else {
			ACP3_CMS::$view->assign('no_search_results', sprintf(ACP3_CMS::$lang->t('search', 'no_search_results'), $_POST['search_term']));
		}

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('search/results.tpl'));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('search_term' => ''));

	$mods = scandir(MODULES_DIR);
	$c_mods = count($mods);
	$search_mods = array();

	for ($i = 0; $i < $c_mods; ++$i) {
		if (ACP3_Modules::check($mods[$i], 'extensions/search') === true) {
			$info = ACP3_Modules::getModuleInfo($mods[$i]);
			$name = $info['name'];
			$search_mods[$name]['dir'] = $mods[$i];
			$search_mods[$name]['checked'] = selectEntry('mods', $mods[$i], $mods[$i], 'checked');
			$search_mods[$name]['name'] = $name;
		}
	}
	ksort($search_mods);
	ACP3_CMS::$view->assign('search_mods', $search_mods);

	// Zu durchsuchende Bereiche
	$search_areas = array();
	$search_areas[0]['id'] = 'title_only';
	$search_areas[0]['value'] = 'title';
	$search_areas[0]['checked'] = selectEntry('area', 'title', 'title', 'checked');
	$search_areas[0]['lang'] = ACP3_CMS::$lang->t('search', 'title_only');
	$search_areas[1]['id'] = 'content_only';
	$search_areas[1]['value'] = 'content';
	$search_areas[1]['checked'] = selectEntry('area', 'content', 'title', 'checked');
	$search_areas[1]['lang'] = ACP3_CMS::$lang->t('search', 'content_only');
	$search_areas[2]['id'] = 'title_content';
	$search_areas[2]['value'] = 'title_content';
	$search_areas[2]['checked'] = selectEntry('area', 'title_content', 'title', 'checked');
	$search_areas[2]['lang'] = ACP3_CMS::$lang->t('search', 'title_and_content');
	ACP3_CMS::$view->assign('search_areas', $search_areas);

	// Treffer sortieren
	$sort_hits = array();
	$sort_hits[0]['id'] = 'asc';
	$sort_hits[0]['value'] = 'asc';
	$sort_hits[0]['checked'] = selectEntry('sort', 'asc', 'asc', 'checked');
	$sort_hits[0]['lang'] = ACP3_CMS::$lang->t('search', 'asc');
	$sort_hits[1]['id'] = 'desc';
	$sort_hits[1]['value'] = 'desc';
	$sort_hits[1]['checked'] = selectEntry('sort', 'desc', 'asc', 'checked');
	$sort_hits[1]['lang'] = ACP3_CMS::$lang->t('search', 'desc');
	ACP3_CMS::$view->assign('sort_hits', $sort_hits);

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('search/list.tpl'));
}
