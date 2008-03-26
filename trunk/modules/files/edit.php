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
		$form = $_POST['form'];
		if (isset($form['external'])) {
			$file = $form['file_external'];
		} elseif (!empty($_FILES['file_internal']['name'])) {
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
		if (!isset($form['external']) && isset($file) && is_array($file) && (empty($file['tmp_name']) || empty($file['size'])))
			$errors[] = lang('files', 'select_internal_resource');
		if (strlen($form['text']) < 3)
			$errors[] = lang('files', 'description_to_short');
		if (!$validate->isNumber($form['cat']) || $validate->isNumber($form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
			$errors[] = lang('files', 'select_category');

		if (isset($errors)) {
			$tpl->assign('error_msg', combo_box($errors));
		} else {
			$new_file_sql = null;
			// Falls eine neue Datei angegeben wurde, Änderungen durchführen
			if (isset($file)) {
				if (is_array($file)) {
					$result = move_file($file['tmp_name'], $file['name'], 'files');
					$new_file = $result['name'];
					$filesize = $result['size'];
				} elseif (is_string($file)) {
					settype($form['filesize'], 'float');
					$new_file = $file;
					$filesize = $form['filesize'] . ' ' . $form['unit'];
				}
				// SQL Query für die Änderungen
				$new_file_sql = array(
					'file' => $new_file,
					'size' => $filesize,
				);
			}
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'category_id' => $form['cat'],
				'link_title' => $db->escape($form['link_title']),
				'text' => $db->escape($form['text'], 2),
			);
			if (is_array($new_file_sql)) {
				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('files', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('files_details_id_' . $modules->id, $db->select('f.id, f.start, f.category_id, f.file, f.size, f.link_title, f.text, c.name AS category_name', 'files AS f, ' . CONFIG_DB_PRE . 'categories AS c', 'f.id = \'' . $modules->id . '\' AND f.category_id = c.id'));

			$content = combo_box($bool ? lang('files', 'edit_success') : lang('files', 'edit_error'), uri('acp/files'));
		}
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
		if ($modules->check('categories', 'functions')) {
			include_once ACP3_ROOT . 'modules/categories/functions.php';
			$tpl->assign('categories', categoriesList('files', 'edit', $dl[0]['category_id']));
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