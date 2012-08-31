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
		$errors['mode'] = $lang->t('menus', 'select_page_type');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = $lang->t('menus', 'title_to_short');
	if (ACP3_Validate::isNumber($_POST['block_id']) === false)
		$errors['block-id'] = $lang->t('menus', 'select_block');
	if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === false)
		$errors['parent'] = $lang->t('menus', 'select_superior_page');
	if (!empty($_POST['parent']) && ACP3_Validate::isNumber($_POST['parent']) === true) {
		// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
		$parent_block = $db->select('block_id', 'menu_items', 'id = \'' . $_POST['parent'] . '\'');
		if (!empty($parent_block) && $parent_block[0]['block_id'] != $_POST['block_id'])
			$errors['parent'] = $lang->t('menus', 'superior_page_not_allowed');
	}
	if ($_POST['display'] != 0 && $_POST['display'] != 1)
		$errors[] = $lang->t('menus', 'select_item_visibility');
	if (ACP3_Validate::isNumber($_POST['target']) === false ||
		$_POST['mode'] == 1 && (is_dir(MODULES_DIR . $_POST['module']) === false || preg_match('=/=', $_POST['module'])) ||
		$_POST['mode'] == 2 && ACP3_Validate::isInternalURI($_POST['uri']) === false ||
		$_POST['mode'] == 3 && empty($_POST['uri']) ||
		$_POST['mode'] == 4 && (ACP3_Validate::isNumber($_POST['static_pages']) === false || $db->countRows('*', 'static_pages', 'id = \'' . $_POST['static_pages'] . '\'') == 0))
		$errors[] = $lang->t('menus', 'type_in_uri_and_target');
	if ($_POST['mode'] == 2 && (bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'mode' => ($_POST['mode'] == 2 || $_POST['mode'] == 3) && preg_match('/^(static_pages\/list\/id_([0-9]+)\/)$/', $_POST['uri']) ? '4' : $_POST['mode'],
			'block_id' => $_POST['block_id'],
			'parent_id' => $_POST['parent'],
			'display' => $_POST['display'],
			'title' => $db->escape($_POST['title']),
			'uri' => $_POST['mode'] == 1 ? $_POST['module'] : ($_POST['mode'] == 4 ? 'static_pages/list/id_' . $_POST['static_pages'] . '/' : $db->escape($_POST['uri'], 2)),
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
				$keywords = $db->escape($_POST['seo_keywords']);
				$description = $db->escape($_POST['seo_description']);
			}
			ACP3_SEO::insertUriAlias($_POST['mode'] == 1 ? '' : $alias, $_POST['mode'] == 1 ? $_POST['module'] : $_POST['uri'], $keywords, $description, (int) $_POST['seo_robots']);
		}

		setMenuItemsCache();

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'create_success' : 'create_error'), 'acp/menus');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Seitentyp
	$mode = array();
	$mode[0]['value'] = 1;
	$mode[0]['selected'] = selectEntry('mode', '1');
	$mode[0]['lang'] = $lang->t('menus', 'module');
	$mode[1]['value'] = 2;
	$mode[1]['selected'] = selectEntry('mode', '2');
	$mode[1]['lang'] = $lang->t('menus', 'dynamic_page');
	$mode[2]['value'] = 3;
	$mode[2]['selected'] = selectEntry('mode', '3');
	$mode[2]['lang'] = $lang->t('menus', 'hyperlink');
	if (ACP3_Modules::isActive('static_pages')) {
		$mode[3]['value'] = 4;
		$mode[3]['selected'] = selectEntry('mode', '4');
		$mode[3]['lang'] = $lang->t('menus', 'static_page');
	}
	$tpl->assign('mode', $mode);

	// Block
	$blocks = $db->select('id, title', 'menus');
	$c_blocks = count($blocks);
	for ($i = 0; $i < $c_blocks; ++$i) {
		$blocks[$i]['selected'] = selectEntry('block_id', $blocks[$i]['id']);
	}
	$tpl->assign('blocks', $blocks);

	// Module
	$modules = ACP3_Modules::getActiveModules();
	foreach ($modules as $row) {
		$modules[$row['name']]['selected'] = selectEntry('module', $row['dir']);
	}
	$tpl->assign('modules', $modules);

	// Ziel des Hyperlinks
	$target = array();
	$target[0]['value'] = 1;
	$target[0]['selected'] = selectEntry('target', '1');
	$target[0]['lang'] = $lang->t('common', 'window_self');
	$target[1]['value'] = 2;
	$target[1]['selected'] = selectEntry('target', '2');
	$target[1]['lang'] = $lang->t('common', 'window_blank');
	$tpl->assign('target', $target);

	$display = array();
	$display[0]['value'] = 1;
	$display[0]['selected'] = selectEntry('display', '1', '1', 'checked');
	$display[0]['lang'] = $lang->t('common', 'yes');
	$display[1]['value'] = 0;
	$display[1]['selected'] = selectEntry('display', '0', '', 'checked');
	$display[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('display', $display);

	if (ACP3_Modules::check('static_pages', 'functions') === true) {
		require_once MODULES_DIR . 'static_pages/functions.php';

		$tpl->assign('static_pages', staticPagesList());
	}

	$defaults = array(
		'title' => '',
		'alias' => '',
		'uri' => '',
		'seo_keywords' => '',
		'seo_description' => '',
	);

	// Daten an Smarty übergeben
	$tpl->assign('pages_list', menuItemsList());
	$tpl->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields());
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('menus/acp_create_item.tpl'));
}
