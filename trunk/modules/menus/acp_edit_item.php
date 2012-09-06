<?php
/**
 * Menu bars
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'menu_items WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	require_once MODULES_DIR . 'menus/functions.php';

	$page = ACP3_CMS::$db2->fetchAssoc('SELECT id, mode, block_id, parent_id, left_id, right_id, display, title, uri, target FROM ' . DB_PRE . 'menu_items WHERE id = ?', array(ACP3_CMS::$uri->id));
	$page['alias'] = $page['mode'] == 2 || $page['mode'] == 4 ? ACP3_SEO::getUriAlias($page['uri'], true) : '';
	$page['seo_keywords'] = ACP3_SEO::getKeywords($page['uri']);
	$page['seo_description'] = ACP3_SEO::getDescription($page['uri']);

	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::isNumber($_POST['mode']) === false)
			$errors['mode'] = ACP3_CMS::$lang->t('menus', 'select_page_type');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3_CMS::$lang->t('menus', 'title_to_short');
		if (ACP3_Validate::isNumber($_POST['block_id']) === false)
			$errors['block-id'] = ACP3_CMS::$lang->t('menus', 'select_menu_bar');
		if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === false)
			$errors['parent'] = ACP3_CMS::$lang->t('menus', 'select_superior_page');
		if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === true) {
			// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
			$parent_block = ACP3_CMS::$db2->fetchColumn('SELECT block_id FROM ' . DB_PRE . 'menu_items WHERE id = ?', array($_POST['parent']));
			if (!empty($parent_block) && $parent_block != $_POST['block_id'])
				$errors[] = ACP3_CMS::$lang->t('menus', 'superior_page_not_allowed');
		}
		if ($_POST['display'] != 0 && $_POST['display'] != 1)
			$errors['display'] = ACP3_CMS::$lang->t('menus', 'select_item_visibility');
		if (ACP3_Validate::isNumber($_POST['target']) === false ||
			$_POST['mode'] == 1 && (is_dir(MODULES_DIR . $_POST['module']) === false || preg_match('=/=', $_POST['module'])) ||
			$_POST['mode'] == 2 && ACP3_Validate::isInternalURI($_POST['uri']) === false ||
			$_POST['mode'] == 3 && empty($_POST['uri']) ||
			$_POST['mode'] == 4 && (ACP3_Validate::isNumber($_POST['articles']) === false || ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array($_POST['articles'])) == 0))
			$errors[] = ACP3_CMS::$lang->t('menus', 'type_in_uri_and_target');
		if (($_POST['mode'] == 2 || $_POST['mode'] == 4) && (bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], $_POST['uri'])))
			$errors['alias'] = ACP3_CMS::$lang->t('system', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			// Vorgenommene Änderungen am Datensatz anwenden
			$mode = ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match('/^(articles\/list\/id_([0-9]+)\/)$/', $_POST['uri']) ? '4' : $_POST['mode'];
			$uri_type = $_POST['mode'] == 4 ? 'articles/list/id_' . $_POST['articles'] . '/' : $_POST['uri'];

			$update_values = array(
				'mode' => $mode,
				'block_id' => $_POST['block_id'],
				'parent_id' => $_POST['parent'],
				'display' => $_POST['display'],
				'title' => $_POST['title'],
				'uri' => $_POST['mode'] == 1 ? $_POST['module'] : $uri_type,
				'target' => $_POST['display'] == 0 ? 1 : $_POST['target'],
			);

			$nestedSet = new ACP3_NestedSet('menu_items', true);
			$bool = $nestedSet->editNode(ACP3_CMS::$uri->id, (int) $_POST['parent'], (int) $_POST['block_id'], $update_values);

			// Verhindern, dass externe URIs Aliase, Keywords, etc. zugewiesen bekommen
			if ($_POST['mode'] != 3) {
				$alias = $_POST['alias'] === $page['alias'] ? $page['alias'] : $_POST['alias'];
				$keywords = $_POST['seo_keywords'] === $page['seo_keywords'] ? $page['seo_keywords'] : $_POST['seo_keywords'];
				$description = $_POST['seo_description'] === $page['seo_description'] ? $page['seo_description'] : $_POST['seo_description'];
				ACP3_SEO::insertUriAlias($_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'], $_POST['mode'] == 1 ? '' : $alias, $keywords, $description, (int) $_POST['seo_robots']);
			}

			setMenuItemsCache();

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/menus');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		// Seitentyp
		$mode = array();
		$mode[0]['value'] = 1;
		$mode[0]['selected'] = selectEntry('mode', '1', $page['mode']);
		$mode[0]['lang'] = ACP3_CMS::$lang->t('menus', 'module');
		$mode[1]['value'] = 2;
		$mode[1]['selected'] = selectEntry('mode', '2', $page['mode']);
		$mode[1]['lang'] = ACP3_CMS::$lang->t('menus', 'dynamic_page');
		$mode[2]['value'] = 3;
		$mode[2]['selected'] = selectEntry('mode', '3', $page['mode']);
		$mode[2]['lang'] = ACP3_CMS::$lang->t('menus', 'hyperlink');
		if (ACP3_Modules::isActive('articles')) {
			$mode[3]['value'] = 4;
			$mode[3]['selected'] = selectEntry('mode', '4', $page['mode']);
			$mode[3]['lang'] = ACP3_CMS::$lang->t('menus', 'article');
		}
		ACP3_CMS::$view->assign('mode', $mode);

		// Block
		ACP3_CMS::$view->assign('blocks', menusDropdown($page['block_id']));

		// Module
		$modules = ACP3_Modules::getAllModules();
		foreach ($modules as $row) {
			$modules[$row['name']]['selected'] = selectEntry('module', $row['dir'], $page['mode'] == 1 ? $page['uri'] : '');
		}
		ACP3_CMS::$view->assign('modules', $modules);

		if ($page['mode'] == 1)
			$page['uri'] = '';

		// Ziel des Hyperlinks
		$target = array();
		$target[0]['value'] = 1;
		$target[0]['selected'] = selectEntry('target', '1', $page['target']);
		$target[0]['lang'] = ACP3_CMS::$lang->t('system', 'window_self');
		$target[1]['value'] = 2;
		$target[1]['selected'] = selectEntry('target', '2', $page['target']);
		$target[1]['lang'] = ACP3_CMS::$lang->t('system', 'window_blank');
		ACP3_CMS::$view->assign('target', $target);

		$display = array();
		$display[0]['value'] = 1;
		$display[0]['selected'] = selectEntry('display', '1', $page['display'], 'checked');
		$display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$display[1]['value'] = 0;
		$display[1]['selected'] = selectEntry('display', '0', $page['display'], 'checked');
		$display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('display', $display);

		if (ACP3_Modules::check('articles', 'functions') === true) {
			require_once MODULES_DIR . 'articles/functions.php';

			$matches = array();
			if (!isset($_POST['submit']) && $page['mode'] == 4) {
				preg_match_all('/^(articles\/list\/id_([0-9]+)\/)$/', $page['uri'], $matches);
			}

			ACP3_CMS::$view->assign('articles', articlesList(!empty($matches[2]) ? $matches[2][0] : ''));
		}

		// Daten an Smarty übergeben
		ACP3_CMS::$view->assign('pages_list', menuItemsList($page['parent_id'], $page['left_id'], $page['right_id']));
		ACP3_CMS::$view->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields($page['uri']));
		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $page);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('menus/acp_edit_item.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
