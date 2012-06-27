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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'menu_items', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'menu_items/functions.php';

	$page = $db->select('id, mode, block_id, parent_id, left_id, right_id, display, title, uri, target', 'menu_items', 'id = \'' . $uri->id . '\'');
	$page[0]['title'] = $db->escape($page[0]['title'], 3);
	$page[0]['uri'] = $db->escape($page[0]['uri'], 3);
	$page[0]['alias'] = $page[0]['mode'] == 2 || $page[0]['mode'] == 4 ? ACP3_SEO::getUriAlias($page[0]['uri'], true) : '';
	$page[0]['seo_keywords'] = ACP3_SEO::getKeywords($page[0]['uri']);
	$page[0]['seo_description'] = ACP3_SEO::getDescription($page[0]['uri']);

	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::isNumber($_POST['mode']) === false)
			$errors['mode'] = $lang->t('menu_items', 'select_page_type');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = $lang->t('menu_items', 'title_to_short');
		if (ACP3_Validate::isNumber($_POST['block_id']) === false)
			$errors['block-id'] = $lang->t('menu_items', 'select_block');
		if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === false)
			$errors['parent'] = $lang->t('menu_items', 'select_superior_page');
		if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === true) {
			// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
			$parent_block = $db->select('block_id', 'menu_items', 'id = \'' . $_POST['parent'] . '\'');
			if (!empty($parent_block) && $parent_block[0]['block_id'] != $_POST['block_id'])
				$errors[] = $lang->t('menu_items', 'superior_page_not_allowed');
		}
		if ($_POST['display'] != 0 && $_POST['display'] != 1)
			$errors['display'] = $lang->t('menu_items', 'select_item_visibility');
		if (ACP3_Validate::isNumber($_POST['target']) === false ||
			$_POST['mode'] == 1 && (is_dir(MODULES_DIR . $_POST['module']) === false || preg_match('=/=', $_POST['module'])) ||
			$_POST['mode'] == 2 && ACP3_Validate::isInternalURI($_POST['uri']) === false ||
			$_POST['mode'] == 3 && empty($_POST['uri']) ||
			$_POST['mode'] == 4 && (ACP3_Validate::isNumber($_POST['static_pages']) === false || $db->countRows('*', 'static_pages', 'id = \'' . $_POST['static_pages'] . '\'') == 0))
			$errors[] = $lang->t('menu_items', 'type_in_uri_and_target');
		if (($_POST['mode'] == 2 || $_POST['mode'] == 4) && CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], $db->escape($_POST['uri']))))
			$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			// Vorgenommene Änderungen am Datensatz anwenden
			$mode = ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match('/^(static_pages\/list\/id_([0-9]+)\/)$/', $_POST['uri']) ? '4' : $_POST['mode'];
			$uri_type = $_POST['mode'] == 4 ? 'static_pages/list/id_' . $_POST['static_pages'] . '/' : $db->escape($_POST['uri'], 2);

			$update_values = array(
				'mode' => $mode,
				'block_id' => $_POST['block_id'],
				'parent_id' => $_POST['parent'],
				'display' => $_POST['display'],
				'title' => $db->escape($_POST['title']),
				'uri' => $_POST['mode'] == 1 ? $_POST['module'] : $uri_type,
				'target' => $_POST['display'] == 0 ? 1 : $_POST['target'],
			);

			$nestedSet = new ACP3_NestedSet('menu_items', true);
			$bool = $nestedSet->editNode($uri->id, (int) $_POST['parent'], (int) $_POST['block_id'], $update_values);

			// Verhindern, dass externe URIs Aliase, Keywords, etc. zugewiesen bekommen
			if ($_POST['mode'] != 3) {
				$alias = $_POST['alias'] === $page[0]['alias'] ? $page[0]['alias'] : $_POST['alias'];
				$keywords = $_POST['seo_keywords'] === $page[0]['seo_keywords'] ? $page[0]['seo_keywords'] : $_POST['seo_keywords'];
				$description = $_POST['seo_description'] === $page[0]['seo_description'] ? $page[0]['seo_description'] : $_POST['seo_description'];
				ACP3_SEO::insertUriAlias($_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'], $_POST['mode'] == 1 ? '' : $alias, $keywords, $description, (int) $_POST['seo_robots']);
			}

			setMenuItemsCache();

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/menu_items');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
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
		if (ACP3_Modules::isActive('static_pages')) {
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
		$modules = ACP3_Modules::getAllModules();
		foreach ($modules as $row) {
			$modules[$row['name']]['selected'] = selectEntry('module', $row['dir'], $page[0]['mode'] == 1 ? $page[0]['uri'] : '');
		}
		$tpl->assign('modules', $modules);

		if ($page[0]['mode'] == 1)
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

		if (ACP3_Modules::check('static_pages', 'functions') === true) {
			require_once MODULES_DIR . 'static_pages/functions.php';

			$matches = array();
			if (!isset($_POST['submit']) && $page[0]['mode'] == 4) {
				preg_match_all('/^(static_pages\/list\/id_([0-9]+)\/)$/', $page[0]['uri'], $matches);
			}

			$tpl->assign('static_pages', staticPagesList(!empty($matches[2]) ? $matches[2][0] : ''));
		}

		// Daten an Smarty übergeben
		$tpl->assign('enable_uri_aliases', CONFIG_SEO_ALIASES);
		$tpl->assign('pages_list', menuItemsList($page[0]['parent_id'], $page[0]['left_id'], $page[0]['right_id']));
		$tpl->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields($page[0]['uri']));
		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $page[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('menu_items/edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
