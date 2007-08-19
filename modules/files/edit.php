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

if (!empty($modules->id) && $db->select('id', 'files', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/files/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$dl = $db->select('start, end, cat, file, size, link_title, text', 'files', 'id = \'' . $modules->id . '\'');
		$dl[0]['text'] = $db->escape($dl[0]['text'], 3);
		// Datum
		$start_date = explode('.', date_aligned(1, $dl[0]['start'], 'j.n.Y.G.i'));
		$end_date = explode('.', date_aligned(1, $dl[0]['end'], 'j.n.Y.G.i'));

		// Datumsauswahl
		$tpl->assign('start_day', date_dropdown('day', 'start_day', 'start_day', $start_date[0]));
		$tpl->assign('start_month', date_dropdown('month', 'start_month', 'start_month', $start_date[1]));
		$tpl->assign('start_year', date_dropdown('year', 'start_year', 'start_year', $start_date[2]));
		$tpl->assign('start_hour', date_dropdown('hour', 'start_hour', 'start_hour', $start_date[3]));
		$tpl->assign('start_min', date_dropdown('min', 'start_min', 'start_min', $start_date[4]));
		$tpl->assign('end_day', date_dropdown('day', 'end_day', 'end_day', $end_date[0]));
		$tpl->assign('end_month', date_dropdown('month', 'end_month', 'end_month', $end_date[1]));
		$tpl->assign('end_year', date_dropdown('year', 'end_year', 'end_year', $end_date[2]));
		$tpl->assign('end_hour', date_dropdown('hour', 'end_hour', 'end_hour', $end_date[3]));
		$tpl->assign('end_min', date_dropdown('min', 'end_min', 'end_min', $end_date[4]));

		// Formularelemente
		if (!$cache->check('categories_files')) {
			$cache->create('categories_files', $db->select('id, name, description', 'categories', 'module = \'files\'', 'name ASC'));
		}
		$categories = $cache->output('categories_files');
		$c_categories = count($categories);

		if ($c_categories > 0) {
			for ($i = 0; $i < $c_categories; $i++) {
				$categories[$i]['name'] = $categories[$i]['name'];
				$categories[$i]['selected'] = select_entry('cat', $categories[$i]['id'], $dl[0]['cat']);
			}
			$tpl->assign('categories', $categories);
		}

		$tpl->assign('checked_external', isset($form['external']) ? ' checked="checked"' : '');
		$tpl->assign('current_file', $dl[0]['file']);
		$tpl->assign('form', isset($form) ? $form : $dl[0]);

		$content = $tpl->fetch('files/edit.html');
	}
} else {
	redirect('errors/403');
}
?>