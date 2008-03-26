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

	if (!$validate->date($form))
		$errors[] = lang('common', 'select_date');
	if (strlen($form['name']) < 3)
		$errors[] = lang('gallery', 'type_in_gallery_name');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$start_date = dateAligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
		$end_date = dateAligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

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
	$tpl->assign('start_date', publicationPeriod('start'));
	$tpl->assign('end_date', publicationPeriod('end'));

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('gallery/create.html');
}
?>