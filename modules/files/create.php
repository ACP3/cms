<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];
	if (isset($form['external'])) {
		$file = $form['file_external'];
	} else {
		$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
		$file['name'] = $_FILES['file_internal']['name'];
		$file['size'] = $_FILES['file_internal']['size'];
	}

	if (!$validate->date($form))
		$errors[] = lang('common', 'select_date');
	if (strlen($form['link_title']) < 3)
		$errors[] = lang('files', 'type_in_link_title');
	if (isset($form['external']) && (empty($file) || empty($form['filesize']) || empty($form['unit'])))
		$errors[] = lang('files', 'type_in_external_resource');
	if (!isset($form['external']) && (empty($file['tmp_name']) || empty($file['size'])))
		$errors[] = lang('files', 'select_internal_resource');
	if (strlen($form['text']) < 3)
		$errors[] = lang('files', 'description_to_short');
	if (!$validate->isNumber($form['cat']) || $validate->isNumber($form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
		$errors[] = lang('files', 'select_category');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		if (is_array($file)) {
			$result = moveFile($file['tmp_name'], $file['name'], 'files');
			$new_file = $result['name'];
			$filesize = $result['size'];
		} elseif (is_string($file)) {
			settype($form['filesize'], 'float');
			$new_file = $file;
			$filesize = $form['filesize'] . ' ' . $form['unit'];
		}
		$start_date = dateAligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
		$end_date = dateAligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

		$insert_values = array(
			'id' => '',
			'start' => $start_date,
			'end' => $end_date,
			'category_id' => $form['cat'],
			'file' => $new_file,
			'size' => $filesize,
			'link_title' => $db->escape($form['link_title']),
			'text' => $db->escape($form['text'], 2),
		);

		$bool = $db->insert('files', $insert_values);

		$content = comboBox($bool ? lang('files', 'create_success') : lang('files', 'create_error'), uri('acp/files'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', publicationPeriod('start'));
	$tpl->assign('end_date', publicationPeriod('end'));

	$units[0]['value'] = 'Byte';
	$units[0]['selected'] = selectEntry('unit', 'Byte');
	$units[1]['value'] = 'KiB';
	$units[1]['selected'] = selectEntry('unit', 'KiB');
	$units[2]['value'] = 'MiB';
	$units[2]['selected'] = selectEntry('unit', 'MiB');
	$units[3]['value'] = 'GiB';
	$units[3]['selected'] = selectEntry('unit', 'GiB');
	$units[4]['value'] = 'TiB';
	$units[4]['selected'] = selectEntry('unit', 'TiB');
	$tpl->assign('units', $units);

	// Formularelemente
	if ($modules->check('categories', 'functions')) {
		include_once ACP3_ROOT . 'modules/categories/functions.php';
		$tpl->assign('categories', categoriesList('files', 'create'));
	}

	$tpl->assign('checked_external', isset($form['external']) ? ' checked="checked"' : '');
	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('files/create.html');
}
?>