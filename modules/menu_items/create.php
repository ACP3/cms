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

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!validate::date($form['start'], $form['end']))
		$errors[] = $lang->t('common', 'select_date');
	if (!validate::isNumber($form['mode']))
		$errors[] = $lang->t('menu_items', 'select_static_hyperlink');
	if (!validate::isNumber($form['blocks']))
		$errors[] = $lang->t('menu_items', 'select_block');
	if (strlen($form['title']) < 3)
		$errors[] = $lang->t('menu_items', 'title_to_short');
	if (!empty($form['parent']) && !validate::isNumber($form['parent']))
		$errors[] = $lang->t('menu_items', 'select_superior_page');
	if (!empty($form['parent']) && validate::isNumber($form['parent'])) {
		// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
		$parent_block = $db->select('block_id', 'menu_items', 'id = \'' . $form['parent'] . '\'');
		if (!empty($parent_block) && $parent_block[0]['block_id'] != $form['blocks'])
			$errors[] = $lang->t('menu_items', 'superior_page_not_allowed');
	}
	if (($form['mode'] == '2' || $form['mode'] == '3') && (empty($form['uri']) || !validate::isNumber($form['target'])))
		$errors[] = $lang->t('menu_items', 'type_in_uri_and_target');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => $date->timestamp($form['start']),
			'end' => $date->timestamp($form['end']),
			'mode' => $form['mode'],
			'block_id' => $form['blocks'],
			'title' => $db->escape($form['title']),
			'uri' => $db->escape($form['uri'], 2),
			'target' => $form['target'],
		);

		$bool = insertNode($form['parent'], $insert_values);
		setNavbarCache();

		$content = comboBox($bool ? $lang->t('menu_items', 'create_success') : $lang->t('menu_items', 'create_error'), uri('acp/menu_items'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
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

	// Block
	$blocks = $db->select('id, title', 'menu_items_blocks');
	$c_blocks = count($blocks);
	for ($i = 0; $i < $c_blocks; ++$i) {
		$blocks[$i]['selected'] = selectEntry('blocks', $blocks[$i]['id']);
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

	$defaults = array(
		'title' => '',
		'uri' => ''
	);

	// Daten an Smarty übergeben
	$tpl->assign('start_date', datepicker('start'));
	$tpl->assign('end_date', datepicker('end'));
	$tpl->assign('mode', $mode);
	$tpl->assign('blocks', $blocks);
	$tpl->assign('modules', $modules);
	$tpl->assign('target', $target);
	$tpl->assign('form', isset($form) ? $form : $defaults);
	$tpl->assign('pages_list', pagesList());

	$content = $tpl->fetch('menu_items/create.html');
}
?>