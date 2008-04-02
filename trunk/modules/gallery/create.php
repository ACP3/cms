<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!$validate->date($form['start']) || !$validate->date($form['end']))
		$errors[] = lang('common', 'select_date');
	if (strlen($form['name']) < 3)
		$errors[] = lang('gallery', 'type_in_gallery_name');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$start_date = strtotime($form['start'], dateAligned(2, time()));
		$end_date = strtotime($form['end'], dateAligned(2, time()));

		$insert_values = array(
			'id' => '',
			'start' => $start_date,
			'end' => $end_date,
			'name' => $db->escape($form['name']),
		);

		$bool = $db->insert('gallery', $insert_values);

		$content = comboBox($bool ? lang('gallery', 'create_success') : lang('gallery', 'create_error'), uri('acp/gallery'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', datepicker('start'));
	$tpl->assign('end_date', datepicker('end'));

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('gallery/create.html');
}
?>