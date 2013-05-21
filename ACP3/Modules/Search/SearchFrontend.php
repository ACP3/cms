<?php

namespace ACP3\Modules\Search;

use ACP3\Core;

/**
 * Description of SearchFrontend
 *
 * @author Tino
 */
class SearchFrontend extends Core\ModuleController {

	public function actionList()
	{
		if (isset($_POST['submit']) === true) {
			if (strlen($_POST['search_term']) < 3)
				$errors['search-term'] = Core\Registry::get('Lang')->t('search', 'search_term_to_short');
			if (empty($_POST['mods']))
				$errors[] = Core\Registry::get('Lang')->t('search', 'no_module_selected');
			if (empty($_POST['area']))
				$errors[] = Core\Registry::get('Lang')->t('search', 'no_area_selected');
			if (empty($_POST['sort']) || $_POST['sort'] != 'asc' && $_POST['sort'] != 'desc')
				$errors[] = Core\Registry::get('Lang')->t('search', 'no_sorting_selected');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} else {
				Core\Registry::get('Breadcrumb')
						->append(Core\Registry::get('Lang')->t('search', 'search'), Core\Registry::get('URI')->route('search'))
						->append(Core\Registry::get('Lang')->t('search', 'search_results'));

				$_POST['search_term'] = Core\Functions::str_encode($_POST['search_term']);
				$_POST['sort'] = strtoupper($_POST['sort']);
				$search_results = array();
				foreach ($_POST['mods'] as $module) {
					$action = $module . 'Search';
					if (method_exists("\\ACP3\\Modules\Search\SearchExtensions", $action) &&
						Core\Modules::hasPermission($module, 'list') === true) {
						$results = new SearchExtensions($_POST['area'], $_POST['sort'], $_POST['search_term']);
						$search_results = array_merge($search_results, $results->$action());
					}
				}
				if (!empty($search_results)) {
					ksort($search_results);
					Core\Registry::get('View')->assign('results_mods', $search_results);
				} else {
					Core\Registry::get('View')->assign('no_search_results', sprintf(Core\Registry::get('Lang')->t('search', 'no_search_results'), $_POST['search_term']));
				}

				Core\Registry::get('View')->setContentTemplate('search/results.tpl');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('search_term' => ''));

			$className = "\\ACP3\\Modules\\Search\\SearchExtensions";
			$modules = get_class_methods($className);
			$search_mods = array();

			foreach ($modules as $module) {
				$module = substr($module, 0, strpos($module, 'Search'));
				if (Core\Modules::hasPermission($module, 'list') === true) {
					$info = Core\Modules::getModuleInfo($module);
					$name = $info['name'];
					$search_mods[$name]['dir'] = $module;
					$search_mods[$name]['checked'] = Core\Functions::selectEntry('mods', $module, $module, 'checked');
					$search_mods[$name]['name'] = $name;
				}
			}
			ksort($search_mods);
			Core\Registry::get('View')->assign('search_mods', $search_mods);

			// Zu durchsuchende Bereiche
			$lang_search_areas = array(
				Core\Registry::get('Lang')->t('search', 'title_only'),
				Core\Registry::get('Lang')->t('search', 'content_only'),
				Core\Registry::get('Lang')->t('search', 'title_and_content')
			);
			Core\Registry::get('View')->assign('search_areas', Core\Functions::selectGenerator('area', array('title', 'content', 'title_content'), $lang_search_areas, 'title', 'checked'));

			// Treffer sortieren
			$lang_sort_hits = array(Core\Registry::get('Lang')->t('search', 'asc'), Core\Registry::get('Lang')->t('search', 'desc'));
			Core\Registry::get('View')->assign('sort_hits', Core\Functions::selectGenerator('sort', array('asc', 'desc'), $lang_sort_hits, 'asc', 'checked'));
		}
	}

	public function actionSidebar()
	{
		$mods = scandir(MODULES_DIR);
		$c_mods = count($mods);
		$search_mods = array();

		for ($i = 0; $i < $c_mods; ++$i) {
			if ($mods[$i] !== '.' && $mods[$i] !== '..' && Core\Modules::hasPermission($mods[$i], 'extensions/search') === true) {
				$info = Core\Modules::getModuleInfo($mods[$i]);
				$name = $info['name'];
				$search_mods[$name]['dir'] = $mods[$i];
				$search_mods[$name]['checked'] = Core\Functions::selectEntry('mods', $mods[$i], $mods[$i], 'checked');
				$search_mods[$name]['name'] = $name;
			}
		}
		ksort($search_mods);
		Core\Registry::get('View')->assign('search_mods', $search_mods);

		Core\Registry::get('View')->displayTemplate('search/sidebar.tpl');
	}

}