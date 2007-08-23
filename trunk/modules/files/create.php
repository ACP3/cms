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
	include 'modules/files/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_day', date_dropdown('day', 'start_day', 'start_day'));
	$tpl->assign('start_month', date_dropdown('month', 'start_month', 'start_month'));
	$tpl->assign('start_year', date_dropdown('year', 'start_year', 'start_year'));
	$tpl->assign('start_hour', date_dropdown('hour', 'start_hour', 'start_hour'));
	$tpl->assign('start_min', date_dropdown('min', 'start_min', 'start_min'));
	$tpl->assign('end_day', date_dropdown('day', 'end_day', 'end_day'));
	$tpl->assign('end_month', date_dropdown('month', 'end_month', 'end_month'));
	$tpl->assign('end_year', date_dropdown('year', 'end_year', 'end_year'));
	$tpl->assign('end_hour', date_dropdown('hour', 'end_hour', 'end_hour'));
	$tpl->assign('end_min', date_dropdown('min', 'end_min', 'end_min'));

	// Formularelemente
	if (!$cache->check('categories_files')) {
		$cache->create('categories_files', $db->select('id, name, description', 'categories', 'module = \'files\'', 'name ASC'));
	}
	$categories = $cache->output('categories_files');
	$c_categories = count($categories);

	if ($c_categories > 0) {
		for ($i = 0; $i < $c_categories; $i++) {
			$categories[$i]['name'] = $categories[$i]['name'];
			$categories[$i]['selected'] = select_entry('cat', $categories[$i]['id']);
		}
		$tpl->assign('categories', $categories);
	}

	$tpl->assign('checked_external', isset($form['external']) ? ' checked="checked"' : '');
	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('files/create.html');
}
?>