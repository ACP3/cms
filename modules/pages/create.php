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
		$errors[] = $lang->t('pages', 'select_static_hyperlink');
	if (!validate::isNumber($form['blocks']))
		$errors[] = $lang->t('pages', 'select_block');
	if (strlen($form['title']) < 3)
		$errors[] = $lang->t('pages', 'title_to_short');
	if (!empty($form['parent']) && !validate::isNumber($form['parent']))
		$errors[] = $lang->t('pages', 'select_superior_page');
	if (!empty($form['parent']) && validate::isNumber($form['parent'])) {
		// Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
		$parent_block = $db->select('block_id', 'pages', 'id = \'' . $form['parent'] . '\'');
		if (!empty($form['blocks']) && !empty($parent_block) && $parent_block[0]['block_id'] != $form['blocks'])
			$errors[] = $lang->t('pages', 'superior_page_not_allowed');
	}
	if ($form['mode'] == '1' && strlen($form['text']) < 3)
		$errors[] = $lang->t('pages', 'text_to_short');
	if (($form['mode'] == '2' || $form['mode'] == '3') && (empty($form['uri']) || !validate::isNumber($form['target'])))
		$errors[] = $lang->t('pages', 'type_in_uri_and_target');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		if ($form['mode'] == '1') {
			$form['uri'] = '';
			$form['target'] = '';
		} else {
			$form['text'] = '';
		}

		if (empty($form['parent'])) {
			$left_id = $db->select('left_id', 'pages', 0, 'left_id DESC', 1);
			$right_id = $left_id[0]['left_id'] + 1;
		} else {
			$node = $db->query('SELECT right_id FROM ' . CONFIG_DB_PRE . 'pages WHERE id = \'' . $form['parent'] . '\'');
			$db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET right_id = right_id + 2 WHERE right_id >= ' . $node[0]['right_id'], 0);
			$db->query('UPDATE ' . CONFIG_DB_PRE . 'pages SET left_id = left_id + 2 WHERE left_id > ' . $node[0]['right_id'], 0);
			$left_id = $node[0]['right_id'];
			$right_id = $node[0]['right_id'] + 1;
		}
		$insert_values = array(
			'id' => '',
			'start' => $date->timestamp($form['start']),
			'end' => $date->timestamp($form['end']),
			'mode' => $form['mode'],
			'block_id' => $form['blocks'],
			'left_id' => $left_id,
			'right_id' => $right_id,
			'title' => $db->escape($form['title']),
			'uri' => $db->escape($form['uri'], 2),
			'target' => $form['target'],
			'text' => $db->escape($form['text'], 2),
		);

		$bool = $db->insert('pages', $insert_values);

		setNavbarCache();

		$content = comboBox($bool ? $lang->t('pages', 'create_success') : $lang->t('pages', 'create_error'), uri('acp/pages'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', datepicker('start'));
	$tpl->assign('end_date', datepicker('end'));

	// Seitentyp
	$mode[0]['value'] = 1;
	$mode[0]['selected'] = selectEntry('mode', '1');
	$mode[0]['lang'] = $lang->t('pages', 'static_page');
	$mode[1]['value'] = 2;
	$mode[1]['selected'] = selectEntry('mode', '2');
	$mode[1]['lang'] = $lang->t('pages', 'dynamic_page');
	$mode[2]['value'] = 3;
	$mode[2]['selected'] = selectEntry('mode', '3');
	$mode[2]['lang'] = $lang->t('pages', 'hyperlink');
	$tpl->assign('mode', $mode);

	// Block
	$blocks = $db->select('id, title', 'pages_blocks');
	$c_blocks = count($blocks);
	for ($i = 0; $i < $c_blocks; ++$i) {
		$blocks[$i]['selected'] = selectEntry('blocks', $blocks[$i]['id']);
	}
	$blocks[$c_blocks]['id'] = 0;
	$blocks[$c_blocks]['index_name'] = 'dot_display';
	$blocks[$c_blocks]['selected'] = selectEntry('blocks', '0');
	$blocks[$c_blocks]['title'] = $lang->t('pages', 'do_not_display');
	$tpl->assign('blocks', $blocks);

	// Ziel des Hyperlinks
	$target[0]['value'] = 1;
	$target[0]['selected'] = selectEntry('target', '1');
	$target[0]['lang'] = $lang->t('common', 'window_self');
	$target[1]['value'] = 2;
	$target[1]['selected'] = selectEntry('target', '2');
	$target[1]['lang'] = $lang->t('common', 'window_blank');
	$tpl->assign('target', $target);

	$defaults = array(
		'title' => '',
		'sort' => '',
		'text' => '',
		'uri' => ''
	);

	$tpl->assign('form', isset($form) ? $form : $defaults);
	$tpl->assign('pages_list', pagesList(2));

	$content = $tpl->fetch('pages/create.html');
}
?>