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

if (!empty($modules->id) && $db->select('id', 'files', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/files/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$dl = $db->select('start, end, category_id, file, size, link_title, text', 'files', 'id = \'' . $modules->id . '\'');
		$dl[0]['text'] = $db->escape($dl[0]['text'], 3);
		// Datumsauswahl
		$tpl->assign('start_date', publication_period('start', $dl[0]['start']));
		$tpl->assign('end_date', publication_period('end', $dl[0]['end']));

		$unit = trim(strrchr($dl[0]['size'], ' '));

		$units[0]['value'] = 'Byte';
		$units[0]['selected'] = select_entry('unit', 'Byte', $unit);
		$units[1]['value'] = 'KiB';
		$units[1]['selected'] = select_entry('unit', 'KiB', $unit);
		$units[2]['value'] = 'MiB';
		$units[2]['selected'] = select_entry('unit', 'MiB', $unit);
		$units[3]['value'] = 'GiB';
		$units[3]['selected'] = select_entry('unit', 'GiB', $unit);
		$units[4]['value'] = 'TiB';
		$units[4]['selected'] = select_entry('unit', 'TiB', $unit);
		$tpl->assign('units', $units);

		$dl[0]['filesize'] = substr($dl[0]['size'], 0, strpos($dl[0]['size'], ' '));

		// Formularelemente
		if (!$cache->check('categories_files')) {
			$cache->create('categories_files', $db->select('id, name, description', 'categories', 'module = \'files\'', 'name ASC'));
		}
		$categories = $cache->output('categories_files');
		$c_categories = count($categories);

		if ($c_categories > 0) {
			for ($i = 0; $i < $c_categories; $i++) {
				$categories[$i]['name'] = $categories[$i]['name'];
				$categories[$i]['selected'] = select_entry('cat', $categories[$i]['id'], $dl[0]['category_id']);
			}
			$tpl->assign('categories', $categories);
		}

		$tpl->assign('checked_external', isset($form['external']) ? ' checked="checked"' : '');
		$tpl->assign('current_file', $dl[0]['file']);
		$tpl->assign('form', isset($form) ? $form : $dl[0]);

		$content = $tpl->fetch('files/acp_edit.html');
	}
} else {
	redirect('errors/403');
}
?>