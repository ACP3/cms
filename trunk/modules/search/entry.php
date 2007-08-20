<?php
/**
 * Search
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;
if (!$modules->check(0, 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'search':
		$form = $_POST['form'];
		$i = 0;

		if (strlen($form['search_term']) < 3)
			$errors[$i++] = lang('search', 'search_term_to_short');
		if (empty($form['mods']))
			$errors[$i++] = lang('search', 'no_module_selected');
		if (empty($form['area']))
			$errors[$i++] = lang('search', 'no_area_selected');
		if (empty($form['sort']) || $form['sort'] != 'asc' && $form['sort'] != 'desc')
			$errors[$i++] = lang('search', 'no_hits_sorting_selected');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$form['sort'] = strtoupper($form['sort']);
			$results_mods = array();
			foreach ($form['mods'] as $search_mod) {
				if ($modules->is_active($search_mod) && is_file('modules/search/modules/' . $search_mod . '.php')) {
					include 'modules/search/modules/' . $search_mod . '.php';
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