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
		$errors['search-term'] = $lang->t('search', 'search_term_to_short');
	if (empty($_POST['mods']))
		$errors[] = $lang->t('search', 'no_module_selected');
	if (empty($_POST['area']))
		$errors[] = $lang->t('search', 'no_area_selected');
	if (empty($_POST['sort']) || $_POST['sort'] != 'asc' && $_POST['sort'] != 'desc')
		$errors[] = $lang->t('search', 'no_sorting_selected');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} else {
		$breadcrumb->append($lang->t('search', 'search'), $uri->route('search'))
				   ->append($lang->t('search', 'search_results'));

		$_POST['sort'] = strtoupper($_POST['sort']);
		$results_mods = array();
		foreach ($_POST['mods'] as $module) {
			if (ACP3_Modules::check($module, 'extensions/search') === true) {
				include MODULES_DIR . $module . '/extensions/search.php';
			}
		}
		if (!empty($results_mods))
			$tpl->assign('results_mods', $results_mods);
		else
			$tpl->assign('no_search_results', sprintf($lang->t('search', 'no_search_results'), $_POST['search_term']));

		ACP3_View::setContent(ACP3_View::fetchTemplate('search/results.tpl'));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('search_term' => ''));

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
	$tpl->assign('search_mods', $search_mods);

	// Zu durchsuchende Bereiche
	$search_areas = array();
	$search_areas[0]['id'] = 'title_only';
	$search_areas[0]['value'] = 'title';
	$search_areas[0]['checked'] = selectEntry('area', 'title', 'title', 'checked');
	$search_areas[0]['lang'] = $lang->t('search', 'title_only');
	$search_areas[1]['id'] = 'content_only';
	$search_areas[1]['value'] = 'content';
	$search_areas[1]['checked'] = selectEntry('area', 'content', 'title', 'checked');
	$search_areas[1]['lang'] = $lang->t('search', 'content_only');
	$search_areas[2]['id'] = 'title_content';
	$search_areas[2]['value'] = 'title_content';
	$search_areas[2]['checked'] = selectEntry('area', 'title_content', 'title', 'checked');
	$search_areas[2]['lang'] = $lang->t('search', 'title_and_content');
	$tpl->assign('search_areas', $search_areas);

	// Treffer sortieren
	$sort_hits = array();
	$sort_hits[0]['id'] = 'asc';
	$sort_hits[0]['value'] = 'asc';
	$sort_hits[0]['checked'] = selectEntry('sort', 'asc', 'asc', 'checked');
	$sort_hits[0]['lang'] = $lang->t('search', 'asc');
	$sort_hits[1]['id'] = 'desc';
	$sort_hits[1]['value'] = 'desc';
	$sort_hits[1]['checked'] = selectEntry('sort', 'desc', 'asc', 'checked');
	$sort_hits[1]['lang'] = $lang->t('search', 'desc');
	$tpl->assign('sort_hits', $sort_hits);

	ACP3_View::setContent(ACP3_View::fetchTemplate('search/list.tpl'));
}
