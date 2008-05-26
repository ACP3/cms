<?php
/**
 * News
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
		$errors[] = lang('common', 'select_date');
	if (strlen($form['headline']) < 3)
		$errors[] = lang('news', 'headline_to_short');
	if (strlen($form['text']) < 3)
		$errors[] = lang('news', 'text_to_short');
	if (!validate::isNumber($form['cat']) || validate::isNumber($form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
		$errors[] = lang('news', 'select_category');
	if (!empty($form['uri']) && (!validate::isNumber($form['target']) || strlen($form['link_title']) < 3))
		$errors[] = lang('news', 'complete_additional_hyperlink_statements');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$start_date = strtotime($form['start'], dateAligned(2, time()));
		$end_date = strtotime($form['end'], dateAligned(2, time()));

		$insert_values = array(
			'id' => '',
			'start' => $start_date,
			'end' => $end_date,
			'headline' => $db->escape($form['headline']),
			'text' => $db->escape($form['text'], 2),
			'category_id' => $form['cat'],
			'uri' => $db->escape($form['uri'], 2),
			'target' => $form['target'],
			'link_title' => $db->escape($form['link_title'])
		);

		$bool = $db->insert('news', $insert_values);

		$content = comboBox($bool ? lang('news', 'create_success') : lang('news', 'create_error'), uri('acp/news'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', datepicker('start'));
	$tpl->assign('end_date', datepicker('end'));

	// Kategorien
	if (modules::check('categories', 'functions')) {
		include_once ACP3_ROOT . 'modules/categories/functions.php';
		$tpl->assign('categories', categoriesList('news', 'create'));
	}
	
	// Linkziel
	$target[0]['value'] = '1';
	$target[0]['selected'] = selectEntry('target', '1');
	$target[0]['lang'] = lang('common', 'window_self');
	$target[1]['value'] = '2';
	$target[1]['selected'] = selectEntry('target', '2');
	$target[1]['lang'] = lang('common', 'window_blank');
	$tpl->assign('target', $target);

	$tpl->assign('form', isset($form) ? $form : array('headline' => '', 'text' => '', 'uri' => '', 'link_title' => ''));

	$content = $tpl->fetch('news/create.html');
}
?>