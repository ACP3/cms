<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

require_once ACP3_ROOT . 'modules/menu_items/functions.php';

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (!validate::date($form['start'], $form['end']))
		$errors[] = $lang->t('common', 'select_date');
	if (!validate::isNumber($form['mode']))
		$errors[] = $lang->t('menu_items', 'select_page_type');
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
		$form['mode'] == '1' && (!is_dir(ACP3_ROOT . 'modules/' . $form['module']) || preg_match('=/=', $form['module'])) ||
		$form['mode'] == '2' && !validate::internalURI($form['uri']) ||
		$form['mode'] == '3' && empty($form['uri']) ||
		$form['mode'] == '4' && (!validate::isNumber($form['static_pages']) || $db->countRows('*', 'static_pages', 'id = \'' . $form['static_pages'] . '\'') == 0))
		$errors[] = $lang->t('menu_items', 'type_in_uri_and_target');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => $date->timestamp($form['start']),
			'end' => $date->timestamp($form['end']),
			'mode' => ($form['mode'] == '2' || $form['mode'] == '3') && preg_match('/^(static_pages\/list\/id_([0-9]+)\/)$/', $form['uri']) ? '4' : $form['mode'],
			'block_id' => $form['block_id'],
			'display' => $form['display'],
			'title' => db::escape($form['title']),
			'uri' => $form['mode'] == '1' ? $form['module'] : ($form['mode'] == '4' ? 'static_pages/list/id_' . $form['static_pages'] . '/' : db::escape($form['uri'], 2)),
			'target' => $form['target'],
		);

		$bool = insertNode($form['parent'], $insert_values);
		setMenuItemsCache();

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/menu_items'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	// Seitentyp
	$mode[0]['value'] = 1;
	$mode[0]['selected'] = selectEntry('mode', '1');
	$mode[0]['lang'] = $lang->t('menu_items', 'module');
	$mode[1]['value'] = 2;
	$mode[1]['selected'] = selectEntry('mode', '2');
	$mode[1]['lang'] = $lang->t('menu_items', 'dynamic_page');
	$mode[2]['value'] = 3;
	$mode[2]['selected'] = selectEntry('mode', '3');
	$mode[2]['lang'] = $lang->t('menu_items', 'hyperlink');
	if (modules::check('static_pages', 'functions')) {
		$mode[3]['value'] = 4;
		$mode[3]['selected'] = selectEntry('mode', '4');
		$mode[3]['lang'] = $lang->t('menu_items', 'static_page');
	}

	// Block
	$blocks = $db->select('id, title', 'menu_items_blocks');
	$c_blocks = count($blocks);
	for ($i = 0; $i < $c_blocks; ++$i) {
		$blocks[$i]['selected'] = selectEntry('block_id', $blocks[$i]['id']);
	}

	// Module
	$modules = modules::modulesList();
	foreach ($modules as $row) {
		$modules[$row['name']]['selected'] = selectEntry('module', $row['dir']);
	}

	// Ziel des Hyperlinks
	$target[0]['value'] = 1;
	$target[0]['selected'] = selectEntry('target', '1');
	$target[0]['lang'] = $lang->t('common', 'window_self');
	$target[1]['value'] = 2;
	$target[1]['selected'] = selectEntry('target', '2');
	$target[1]['lang'] = $lang->t('common', 'window_blank');

	$display[0]['value'] = 1;
	$display[0]['selected'] = selectEntry('display', '1', '1', 'checked');
	$display[0]['lang'] = $lang->t('common', 'yes');
	$display[1]['value'] = 0;
	$display[1]['selected'] = selectEntry('display', '0', '', 'checked');
	$display[1]['lang'] = $lang->t('common', 'no');

	if (modules::check('static_pages', 'functions')) {
		require_once ACP3_ROOT . 'modules/static_pages/functions.php';

		$tpl->assign('static_pages', getStaticPages());
	}

	$defaults = array(
		'title' => '',
		'uri' => ''
	);

	// Daten an Smarty übergeben
	$tpl->assign('publication_period', datepicker(array('start', 'end')));
	$tpl->assign('mode', $mode);
	$tpl->assign('blocks', $blocks);
	$tpl->assign('modules', $modules);
	$tpl->assign('target', $target);
	$tpl->assign('display', $display);
	$tpl->assign('form', isset($form) ? $form : $defaults);
	$tpl->assign('pages_list', pagesList());

	$content = modules::fetchTemplate('menu_items/create.html');
}
