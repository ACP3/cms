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

require_once MODULES_DIR . 'menu_items/functions.php';

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (validate::isNumber($form['mode']) === false)
		$errors[] = $lang->t('menu_items', 'select_page_type');
	if ($form['mode'] == 2 && CONFIG_SEO_ALIASES === true && !empty($form['alias']) && (validate::isUriSafe($form['alias']) === false || validate::uriAliasExists($form['alias'])))
		$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');
	if (strlen($form['title']) < 3)
		$errors[] = $lang->t('menu_items', 'title_to_short');
	if (validate::isNumber($form['block_id']) === false)
		$errors[] = $lang->t('menu_items', 'select_block');
	if (!empty($form['parent']) && validate::isNumber($form['parent']) === false)
		$errors[] = $lang->t('menu_items', 'select_superior_page');
	if (!empty($form['parent']) && validate::isNumber($form['parent']) === true) {
		// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
		$parent_block = $db->select('block_id', 'menu_items', 'id = \'' . $form['parent'] . '\'');
		if (!empty($parent_block) && $parent_block[0]['block_id'] != $form['block_id'])
			$errors[] = $lang->t('menu_items', 'superior_page_not_allowed');
	}
	if ($form['display'] != 0 && $form['display'] != 1)
		$errors[] = $lang->t('menu_items', 'select_item_visibility');
	if (validate::isNumber($form['target']) === false ||
		$form['mode'] == 1 && (is_dir(MODULES_DIR . $form['module']) === false || preg_match('=/=', $form['module'])) ||
		$form['mode'] == 2 && validate::isInternalURI($form['uri']) === false ||
		$form['mode'] == 3 && empty($form['uri']) ||
		$form['mode'] == 4 && (validate::isNumber($form['static_pages']) === false || $db->countRows('*', 'static_pages', 'id = \'' . $form['static_pages'] . '\'') == 0))
		$errors[] = $lang->t('menu_items', 'type_in_uri_and_target');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'mode' => ($form['mode'] == 2 || $form['mode'] == 3) && preg_match('/^(static_pages\/list\/id_([0-9]+)\/)$/', $form['uri']) ? '4' : $form['mode'],
			'block_id' => $form['block_id'],
			'parent_id' => $form['parent'],
			'display' => $form['display'],
			'title' => $db->escape($form['title']),
			'uri' => $form['mode'] == 1 ? $form['module'] : ($form['mode'] == 4 ? 'static_pages/list/id_' . $form['static_pages'] . '/' : $db->escape($form['uri'], 2)),
			'target' => $form['target'],
		);

		$bool = menuItemsInsertNode($form['parent'], $insert_values);

		// Verhindern, dass externe URIs Aliase, Keywords, etc. zugewiesen bekommen
		if ($form['mode'] != 3) {
			if (seo::uriAliasExists($form['uri'])) {
				$alias = !empty($form['alias']) ? $form['alias'] : seo::getUriAlias($form['uri']);
				$keywords = seo::getKeywords($form['uri']);
				$description = seo::getDescription($form['uri']);
			} else {
				$alias = $form['alias'];
				$keywords = $form['seo_keywords'];
				$description = $form['seo_description'];
			}
			seo::insertUriAlias($form['mode'] == 1 ? '' : $alias, $form['mode'] == 1 ? $form['module'] : $form['uri'], $keywords, $description);
		}

		setMenuItemsCache();

		$session->unsetFormToken();

		setRedirectMessage($bool !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), 'acp/menu_items');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	// Seitentyp
	$mode = array();
	$mode[0]['value'] = 1;
	$mode[0]['selected'] = selectEntry('mode', '1');
	$mode[0]['lang'] = $lang->t('menu_items', 'module');
	$mode[1]['value'] = 2;
	$mode[1]['selected'] = selectEntry('mode', '2');
	$mode[1]['lang'] = $lang->t('menu_items', 'dynamic_page');
	$mode[2]['value'] = 3;
	$mode[2]['selected'] = selectEntry('mode', '3');
	$mode[2]['lang'] = $lang->t('menu_items', 'hyperlink');
	if (modules::isActive('static_pages')) {
		$mode[3]['value'] = 4;
		$mode[3]['selected'] = selectEntry('mode', '4');
		$mode[3]['lang'] = $lang->t('menu_items', 'static_page');
	}
	$tpl->assign('mode', $mode);

	// Block
	$blocks = $db->select('id, title', 'menu_items_blocks');
	$c_blocks = count($blocks);
	for ($i = 0; $i < $c_blocks; ++$i) {
		$blocks[$i]['selected'] = selectEntry('block_id', $blocks[$i]['id']);
	}
	$tpl->assign('blocks', $blocks);

	// Module
	$modules = modules::modulesList();
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

	if (modules::check('static_pages', 'functions')) {
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
	$tpl->assign('enable_uri_aliases', CONFIG_SEO_ALIASES);
	$tpl->assign('form', isset($form) ? $form : $defaults);
	$tpl->assign('pages_list', menuItemsList());

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('menu_items/create.tpl'));
}
