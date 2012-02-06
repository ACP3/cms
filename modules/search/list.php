<?php
/**
 * Search
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (strlen($form['search_term']) < 3)
		$errors[] = $lang->t('search', 'search_term_to_short');
	if (empty($form['mods']))
		$errors[] = $lang->t('search', 'no_module_selected');
	if (empty($form['area']))
		$errors[] = $lang->t('search', 'no_area_selected');
	if (empty($form['sort']) || $form['sort'] != 'asc' && $form['sort'] != 'desc')
		$errors[] = $lang->t('search', 'no_sorting_selected');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} else {
		breadcrumb::assign($lang->t('search', 'search'), $uri->route('search'));
		breadcrumb::assign($lang->t('search', 'search_results'));

		$form['sort'] = strtoupper($form['sort']);
		$results_mods = array();
		foreach ($form['mods'] as $module) {
			if (modules::check($module, 'extensions/search') == 1) {
				include MODULES_DIR . $module . '/extensions/search.php';
			}
		}
		if (!empty($results_mods))
			$tpl->assign('results_mods', $results_mods);
		else
			$tpl->assign('no_search_results', sprintf($lang->t('search', 'no_search_results'), $form['search_term']));

		view::setContent(view::fetchTemplate('search/results.tpl'));
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($form) ? $form : array('search_term' => ''));

	$mods = scandir(MODULES_DIR);
	$c_mods = count($mods);
	$search_mods = array();

	for ($i = 0; $i < $c_mods; ++$i) {
		if (modules::check($mods[$i], 'extensions/search') == 1) {
			$info = modules::parseInfo($mods[$i]);
			$name = $info['name'];
			$search_mods[$name]['dir'] = $mods[$i];
			$search_mods[$name]['checked'] = selectEntry('mods', $mods[$i], $mods[$i], 'checked');
			$search_mods[$name]['name'] = $name;
		}
	}
	ksort($search_mods);
	$tpl->assign('search_mods', $search_mods);

	// Zu durchsuchende Bereiche
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
	$sort_hits[0]['id'] = 'asc';
	$sort_hits[0]['value'] = 'asc';
	$sort_hits[0]['checked'] = selectEntry('sort', 'asc', 'asc', 'checked');
	$sort_hits[0]['lang'] = $lang->t('search', 'asc');
	$sort_hits[1]['id'] = 'desc';
	$sort_hits[1]['value'] = 'desc';
	$sort_hits[1]['checked'] = selectEntry('sort', 'desc', 'asc', 'checked');
	$sort_hits[1]['lang'] = $lang->t('search', 'desc');
	$tpl->assign('sort_hits', $sort_hits);

	view::setContent(view::fetchTemplate('search/list.tpl'));
}
