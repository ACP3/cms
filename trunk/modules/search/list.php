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
		$results_mods = array();
		foreach ($_POST['mods'] as $module) {
			if (ACP3_Modules::check($module, 'extensions/search') === true) {
				include_once MODULES_DIR . $module . '/extensions/search.php';
			}
		}
		if (!empty($results_mods)) {
			ksort($results_mods);
			ACP3_CMS::$view->assign('results_mods', $results_mods);
		} else {
			ACP3_CMS::$view->assign('no_search_results', sprintf(ACP3_CMS::$lang->t('search', 'no_search_results'), $_POST['search_term']));
		}

		ACP3_CMS::$view->setContentTemplate('search/results.tpl');
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
	$lang_search_areas = array(
		ACP3_CMS::$lang->t('search', 'title_only'),
		ACP3_CMS::$lang->t('search', 'content_only'),
		ACP3_CMS::$lang->t('search', 'title_and_content')
	);
	ACP3_CMS::$view->assign('search_areas', selectGenerator('area', array('title', 'content', 'title_content'), $lang_search_areas, 'title', 'checked'));

	// Treffer sortieren
	$lang_sort_hits = array(ACP3_CMS::$lang->t('search', 'asc'), ACP3_CMS::$lang->t('search', 'desc'));
	ACP3_CMS::$view->assign('sort_hits', selectGenerator('sort', array('asc', 'desc'), $lang_sort_hits, 'asc', 'checked'));
}
