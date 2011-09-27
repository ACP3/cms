<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (modules::check('menu_items', 'create') == 1)
	require_once MODULES_DIR . 'menu_items/functions.php';

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (!validate::date($form['start'], $form['end']))
		$errors[] = $lang->t('common', 'select_date');
	if (strlen($form['title']) < 3)
		$errors[] = $lang->t('static_pages', 'title_to_short');
	if (strlen($form['text']) < 3)
		$errors[] = $lang->t('static_pages', 'text_to_short');
	if (!validate::isUriSafe($form['alias']) || validate::UriAliasExists($form['alias']))
		$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');
	if (modules::check('menu_items', 'create') == 1) {
		if ($form['create'] != 1 && $form['create'] != '0')
			$errors[] = $lang->t('static_page', 'select_create_menu_item');
		if ($form['create'] == 1) {
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
		}
	}

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$time_start = $date->timestamp($form['start']);
		$time_end = $date->timestamp($form['end']);

		$insert_values = array(
			'id' => '',
			'start' => $time_start,
			'end' => $time_end,
			'title' => $db->escape($form['title']),
			'text' => $db->escape($form['text'], 2),
		);

		$db->link->beginTransaction();
		$bool = $db->insert('static_pages', $insert_values);
		$last_id = $db->link->lastInsertId();
		$bool2 = seo::insertUriAlias($form['alias'], 'static_pages/list/id_' . $last_id, $db->escape($form['seo_keywords']), $db->escape($form['seo_description']));
		$db->link->commit();

		if ($form['create'] == '1' && modules::check('menu_items', 'create') == 1) {
			$insert_values = array(
				'id' => '',
				'start' => $time_start,
				'end' => $time_end,
				'mode' => 4,
				'block_id' => $form['block_id'],
				'display' => $form['display'],
				'title' => $db->escape($form['title']),
				'uri' => 'static_pages/list/id_' . $last_id . '/',
				'target' => 1,
			);

			insertNode($form['parent'], $insert_values);
			setMenuItemsCache();
		}

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/static_pages'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	if (modules::check('menu_items', 'create') == 1) {
		$create[0]['value'] = 1;
		$create[0]['selected'] = selectEntry('create', '1', '0', 'checked');
		$create[0]['lang'] = $lang->t('common', 'yes');
		$create[1]['value'] = 0;
		$create[1]['selected'] = selectEntry('create', '0', '0', 'checked');
		$create[1]['lang'] = $lang->t('common', 'no');

		// Block
		$blocks = $db->select('id, title', 'menu_items_blocks');
		$c_blocks = count($blocks);
		for ($i = 0; $i < $c_blocks; ++$i) {
			$blocks[$i]['selected'] = selectEntry('block_id', $blocks[$i]['id']);
		}

		$display[0]['value'] = 1;
		$display[0]['selected'] = selectEntry('display', 1, 1, 'checked');
		$display[0]['lang'] = $lang->t('common', 'yes');
		$display[1]['value'] = 0;
		$display[1]['selected'] = selectEntry('display', '0', '', 'checked');
		$display[1]['lang'] = $lang->t('common', 'no');

		$tpl->assign('create', $create);
		$tpl->assign('blocks', $blocks);
		$tpl->assign('display', $display);
		$tpl->assign('pages_list', pagesList());
	}

	$tpl->assign('publication_period', $date->datepicker(array('start', 'end')));

	$defaults = array(
		'title' => '',
		'text' => '',
		'alias' => '',
		'seo_keywords' => '',
		'seo_description' => ''
	);

	$tpl->assign('form', isset($form) ? $form : $defaults);

	$content = modules::fetchTemplate('static_pages/create.html');
}
