<?php
/**
 * Menu Items
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'menus/functions.php';

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
			$errors['parent'] = ACP3_CMS::$lang->t('menus', 'superior_page_not_allowed');
	}
	if ($_POST['display'] != 0 && $_POST['display'] != 1)
		$errors[] = ACP3_CMS::$lang->t('menus', 'select_item_visibility');
	if (ACP3_Validate::isNumber($_POST['target']) === false ||
		$_POST['mode'] == 1 && (is_dir(MODULES_DIR . $_POST['module']) === false || preg_match('=/=', $_POST['module'])) ||
		$_POST['mode'] == 2 && ACP3_Validate::isInternalURI($_POST['uri']) === false ||
		$_POST['mode'] == 3 && empty($_POST['uri']) ||
		$_POST['mode'] == 4 && (ACP3_Validate::isNumber($_POST['articles']) === false || ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array($_POST['articles'])) == 0))
		$errors[] = ACP3_CMS::$lang->t('menus', 'type_in_uri_and_target');
	if ($_POST['mode'] == 2 && (bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = ACP3_CMS::$lang->t('system', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'mode' => ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match('/^(articles\/list\/id_([0-9]+)\/)$/', $_POST['uri']) ? '4' : $_POST['mode'],
			'block_id' => (int) $_POST['block_id'],
			'parent_id' => (int) $_POST['parent'],
			'display' => $_POST['display'],
			'title' => str_encode($_POST['title']),
			'uri' => $_POST['mode'] == 1 ? $_POST['module'] : ($_POST['mode'] == 4 ? 'articles/list/id_' . $_POST['articles'] . '/' : $_POST['uri']),
			'target' => $_POST['display'] == 0 ? 1 : $_POST['target'],
		);

		$nestedSet = new ACP3_NestedSet('menu_items', true);
		$bool = $nestedSet->insertNode((int) $_POST['parent'], $insert_values);

		// Verhindern, dass externe URIs Aliase, Keywords, etc. zugewiesen bekommen
		if ($_POST['mode'] != 3) {
			if (ACP3_SEO::uriAliasExists($_POST['uri'])) {
				$alias = !empty($_POST['alias']) ? $_POST['alias'] : ACP3_SEO::getUriAlias($_POST['uri']);
				$keywords = ACP3_SEO::getKeywords($_POST['uri']);
				$description = ACP3_SEO::getDescription($_POST['uri']);
			} else {
				$alias = $_POST['alias'];
				$keywords = $_POST['seo_keywords'];
				$description = $_POST['seo_description'];
			}
			ACP3_SEO::insertUriAlias($_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'], $_POST['mode'] == 1 ? '' : $alias, $keywords, $description, (int) $_POST['seo_robots']);
		}

		setMenuItemsCache();

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Seitentyp
	$mode = array();
	$mode[0]['value'] = 1;
	$mode[0]['selected'] = selectEntry('mode', '1');
	$mode[0]['lang'] = ACP3_CMS::$lang->t('menus', 'module');
	$mode[1]['value'] = 2;
	$mode[1]['selected'] = selectEntry('mode', '2');
	$mode[1]['lang'] = ACP3_CMS::$lang->t('menus', 'dynamic_page');
	$mode[2]['value'] = 3;
	$mode[2]['selected'] = selectEntry('mode', '3');
	$mode[2]['lang'] = ACP3_CMS::$lang->t('menus', 'hyperlink');
	if (ACP3_Modules::isActive('articles')) {
		$mode[3]['value'] = 4;
		$mode[3]['selected'] = selectEntry('mode', '4');
		$mode[3]['lang'] = ACP3_CMS::$lang->t('menus', 'article');
	}
	ACP3_CMS::$view->assign('mode', $mode);

	// Menus
	ACP3_CMS::$view->assign('blocks', menusDropdown());

	// Module
	$modules = ACP3_Modules::getActiveModules();
	foreach ($modules as $row) {
		$modules[$row['name']]['selected'] = selectEntry('module', $row['dir']);
	}
	ACP3_CMS::$view->assign('modules', $modules);

	// Ziel des Hyperlinks
	$target = array();
	$target[0]['value'] = 1;
	$target[0]['selected'] = selectEntry('target', '1');
	$target[0]['lang'] = ACP3_CMS::$lang->t('system', 'window_self');
	$target[1]['value'] = 2;
	$target[1]['selected'] = selectEntry('target', '2');
	$target[1]['lang'] = ACP3_CMS::$lang->t('system', 'window_blank');
	ACP3_CMS::$view->assign('target', $target);

	$display = array();
	$display[0]['value'] = 1;
	$display[0]['selected'] = selectEntry('display', '1', '1', 'checked');
	$display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
	$display[1]['value'] = 0;
	$display[1]['selected'] = selectEntry('display', '0', '', 'checked');
	$display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
	ACP3_CMS::$view->assign('display', $display);

	if (ACP3_Modules::check('articles', 'functions') === true) {
		require_once MODULES_DIR . 'articles/functions.php';

		ACP3_CMS::$view->assign('articles', articlesList());
	}

	$defaults = array(
		'title' => '',
		'alias' => '',
		'uri' => '',
		'seo_keywords' => '',
		'seo_description' => '',
	);

	// Daten an Smarty übergeben
	ACP3_CMS::$view->assign('pages_list', menuItemsList());
	ACP3_CMS::$view->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields());
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('menus/acp_create_item.tpl'));
}
