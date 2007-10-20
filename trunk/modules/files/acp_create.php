<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/files/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', publication_period('start'));
	$tpl->assign('end_date', publication_period('end'));

	$units[0]['value'] = 'Byte';
	$units[0]['selected'] = select_entry('unit', 'Byte');
	$units[1]['value'] = 'KiB';
	$units[1]['selected'] = select_entry('unit', 'KiB');
	$units[2]['value'] = 'MiB';
	$units[2]['selected'] = select_entry('unit', 'MiB');
	$units[3]['value'] = 'GiB';
	$units[3]['selected'] = select_entry('unit', 'GiB');
	$units[4]['value'] = 'TiB';
	$units[4]['selected'] = select_entry('unit', 'TiB');
	$tpl->assign('units', $units);

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

	$content = $tpl->fetch('files/acp_create.html');
}
?>