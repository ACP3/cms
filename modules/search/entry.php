<?php
/**
 * Search
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_FRONTEND'))
	exit;
if (!$modules->check('search', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'search':
		$form = $_POST['form'];

		if (strlen($form['search_term']) < 3)
			$errors[] = lang('search', 'search_term_to_short');
		if (empty($form['mods']))
			$errors[] = lang('search', 'no_module_selected');
		if (empty($form['area']))
			$errors[] = lang('search', 'no_area_selected');
		if (empty($form['sort']) || $form['sort'] != 'asc' && $form['sort'] != 'desc')
			$errors[] = lang('search', 'no_hits_sorting_selected');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$form['sort'] = strtoupper($form['sort']);
			$results_mods = array();
			foreach ($form['mods'] as $module) {
				if ($modules->check($module, 'extensions/search')) {
					include 'modules/' . $module . '/extensions/search.php';
				}
			}
			if (!empty($results_mods))
				$tpl->assign('results_mods', $results_mods);
			else
				$tpl->assign('no_search_results', sprintf(lang('search', 'no_search_results'), $form['search_term']));

			$content = $tpl->fetch('search/results.html');
		}
		break;
	default:
		redirect('errors/404');
}
?>