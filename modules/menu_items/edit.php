<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (validate::isNumber($uri->id) && $db->countRows('*', 'menu_items', 'id = \'' . $uri->id . '\'') == '1') {
	require_once MODULES_DIR . 'menu_items/functions.php';

	$page = $db->select('id, start, end, mode, block_id, parent_id, left_id, right_id, display, title, uri, target', 'menu_items', 'id = \'' . $uri->id . '\'');
	$page[0]['title'] = $db->escape($page[0]['title'], 3);
	$page[0]['uri'] = $db->escape($page[0]['uri'], 3);
	$page[0]['alias'] = $page[0]['mode'] == 2 || $page[0]['mode'] == 4 ? seo::getUriAlias($page[0]['uri']) : '';
	$page[0]['seo_keywords'] = seo::getKeywords($page[0]['uri']);
	$page[0]['seo_description'] = seo::getDescription($page[0]['uri']);

	if (isset($_POST['form']) === true) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (!validate::isNumber($form['mode']))
			$errors[] = $lang->t('menu_items', 'select_page_type');
		if (($form['mode'] == 2 || $form['mode'] == 4) && CONFIG_SEO_ALIASES === true && !empty($form['alias']) && (!validate::isUriSafe($form['alias']) || validate::uriAliasExists($form['alias'], $db->escape($form['uri']))))
			$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');
		if (strlen($form['title']) < 3)
			$errors[] = $lang->t('menu_items', 'title_to_short');
		if (!validate::isNumber($form['block_id']))
			$errors[] = $lang->t('menu_items', 'select_block');
		if (!empty($form['parent']) && !validate::isNumber($form['parent']))
			$errors[] = $lang->t('menu_items', 'select_superior_page');
		if (!empty($form['parent']) && validate::isNumber($form['parent'])) {
			// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
			$parent_block = $db->select('block_id', 'menu_items', 'id = \'' . $form['parent'] . '\'');
			if (!empty($parent_block) && $parent_block[0]['block_id'] != $form['block_id'])
				$errors[] = $lang->t('menu_items', 'superior_page_not_allowed');
		}
		if ($form['display'] != '0' && $form['display'] != '1')
			$errors[] = $lang->t('menu_items', 'select_item_visibility');
		if (!validate::isNumber($form['target']) ||
			$form['mode'] == '1' && (!is_dir(MODULES_DIR . $form['module']) || preg_match('=/=', $form['module'])) ||
			$form['mode'] == '2' && !validate::isInternalURI($form['uri']) ||
			$form['mode'] == '3' && empty($form['uri']) ||
			$form['mode'] == '4' && (!validate::isNumber($form['static_pages']) || $db->countRows('*', 'static_pages', 'id = \'' . $form['static_pages'] . '\'') == 0))
			$errors[] = $lang->t('menu_items', 'type_in_uri_and_target');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', comboBox($errors));
		} elseif (!validate::formToken()) {
			view::setContent(comboBox($lang->t('common', 'form_already_submitted')));
		} else {
			// Vorgenommene Änderungen am Datensatz anwenden
			$mode = ($form['mode'] == '2' || $form['mode'] == '3') && preg_match('/^(static_pages\/list\/id_([0-9]+)\/)$/', $form['uri']) ? '4' : $form['mode'];
			$uri_type = $form['mode'] == '4' ? 'static_pages/list/id_' . $form['static_pages'] . '/' : $db->escape($form['uri'], 2);

			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'mode' => $mode,
				'block_id' => $form['block_id'],
				'parent_id' => $form['parent'],
				'display' => $form['display'],
				'title' => $db->escape($form['title']),
				'uri' => $form['mode'] == '1' ? $form['module'] : $uri_type,
				'target' => $form['target'],
			);

			$bool = menuItemsEditNode($uri->id, $form['parent'], $form['block_id'], $update_values);

			// Verhindern, dass externe URIs Aliase, Keywords, etc zugewiesen bekommen
			if ($form['mode'] != 3) {
				$alias = $form['alias'] === $page[0]['alias'] ? $page[0]['alias'] : $form['alias'];
				$keywords = $form['seo_keywords'] === $page[0]['seo_keywords'] ? $page[0]['seo_keywords'] : $form['seo_keywords'];
				$description = $form['seo_description'] === $page[0]['seo_description'] ? $page[0]['seo_description'] : $form['seo_description'];
				seo::insertUriAlias($form['mode'] == 1 ? '' : $alias, $form['mode'] == 1 ? $form['module'] : $form['uri'], $keywords, $description);
			}

			setMenuItemsCache();

			$session->unsetFormToken();

			setRedirectMessage($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/menu_items');
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		// Seitentyp
		$mode = array();
		$mode[0]['value'] = 1;
		$mode[0]['selected'] = selectEntry('mode', '1', $page[0]['mode']);
		$mode[0]['lang'] = $lang->t('menu_items', 'module');
		$mode[1]['value'] = 2;
		$mode[1]['selected'] = selectEntry('mode', '2', $page[0]['mode']);
		$mode[1]['lang'] = $lang->t('menu_items', 'dynamic_page');
		$mode[2]['value'] = 3;
		$mode[2]['selected'] = selectEntry('mode', '3', $page[0]['mode']);
		$mode[2]['lang'] = $lang->t('menu_items', 'hyperlink');
		if (modules::isActive('static_pages')) {
			$mode[3]['value'] = 4;
			$mode[3]['selected'] = selectEntry('mode', '4', $page[0]['mode']);
			$mode[3]['lang'] = $lang->t('menu_items', 'static_page');
		}
		$tpl->assign('mode', $mode);

		// Block
		$blocks = $db->select('id, title', 'menu_items_blocks', 0, 'title ASC, id ASC');
		$c_blocks = count($blocks);
		for ($i = 0; $i < $c_blocks; ++$i) {
			$blocks[$i]['title'] = $db->escape($blocks[$i]['title'], 3);
			$blocks[$i]['selected'] = selectEntry('block_id', $blocks[$i]['id'], $page[0]['block_id']);
		}
		$tpl->assign('blocks', $blocks);

		// Module
		$modules = modules::modulesList();
		foreach ($modules as $row) {
			$modules[$row['name']]['selected'] = selectEntry('module', $row['dir'], $page[0]['mode'] == '1' ? $page[0]['uri'] : '');
		}
		$tpl->assign('modules', $modules);

		if ($page[0]['mode'] == '1')
			$page[0]['uri'] = '';

		// Ziel des Hyperlinks
		$target = array();
		$target[0]['value'] = 1;
		$target[0]['selected'] = selectEntry('target', '1', $page[0]['target']);
		$target[0]['lang'] = $lang->t('common', 'window_self');
		$target[1]['value'] = 2;
		$target[1]['selected'] = selectEntry('target', '2', $page[0]['target']);
		$target[1]['lang'] = $lang->t('common', 'window_blank');
		$tpl->assign('target', $target);

		$display = array();
		$display[0]['value'] = 1;
		$display[0]['selected'] = selectEntry('display', '1', $page[0]['display'], 'checked');
		$display[0]['lang'] = $lang->t('common', 'yes');
		$display[1]['value'] = 0;
		$display[1]['selected'] = selectEntry('display', '0', $page[0]['display'], 'checked');
		$display[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('display', $display);

		if (modules::check('static_pages', 'functions')) {
			require_once MODULES_DIR . 'static_pages/functions.php';

			if (!isset($form) && $page[0]['mode'] == '4') {
				preg_match_all('/^(static_pages\/list\/id_([0-9]+)\/)$/', $page[0]['uri'], $matches);
			}

			$tpl->assign('static_pages', staticPagesList(!empty($matches[2]) ? $matches[2][0] : ''));
		}

		// Daten an Smarty übergeben
		$tpl->assign('enable_uri_aliases', CONFIG_SEO_ALIASES);
		$tpl->assign('publication_period', $date->datepicker(array('start', 'end'), array($page[0]['start'], $page[0]['end'])));
		$tpl->assign('form', isset($form) ? $form : $page[0]);

		$tpl->assign('pages_list', menuItemsList($page[0]['parent_id'], $page[0]['left_id'], $page[0]['right_id']));

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('menu_items/edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
