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

if (validate::isNumber($uri->id) && $db->countRows('*', 'files', 'id = \'' . $uri->id . '\'') == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];
		if (isset($form['external'])) {
			$file = $form['file_external'];
		} elseif (!empty($_FILES['file_internal']['name'])) {
			$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
			$file['name'] = $_FILES['file_internal']['name'];
			$file['size'] = $_FILES['file_internal']['size'];
		}

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($form['link_title']) < 3)
			$errors[] = $lang->t('files', 'type_in_link_title');
		if (isset($form['external']) && (empty($file) || empty($form['filesize']) || empty($form['unit'])))
			$errors[] = $lang->t('files', 'type_in_external_resource');
		if (!isset($form['external']) && isset($file) && is_array($file) && (empty($file['tmp_name']) || empty($file['size'])))
			$errors[] = $lang->t('files', 'select_internal_resource');
		if (strlen($form['text']) < 3)
			$errors[] = $lang->t('files', 'description_to_short');
		if (strlen($form['cat_create']) < 3 && !categoriesCheck($form['cat']))
			$errors[] = $lang->t('files', 'select_category');
		if (strlen($form['cat_create']) >= 3 && categoriesCheckDuplicate($form['cat_create'], 'files'))
			$errors[] = $lang->t('categories', 'category_already_exists');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$new_file_sql = null;
			// Falls eine neue Datei angegeben wurde, Änderungen durchführen
			if (isset($file)) {
				if (is_array($file)) {
					$result = moveFile($file['tmp_name'], $file['name'], 'files');
					$new_file = $result['name'];
					$filesize = $result['size'];
				} else {
					$form['filesize'] = (float) $form['filesize'];
					$new_file = $file;
					$filesize = $form['filesize'] . ' ' . $db->escape($form['unit']);
				}
				// SQL Query für die Änderungen
				$new_file_sql = array(
					'file' => $new_file,
					'size' => $filesize,
				);
			}

			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'category_id' => strlen($form['cat_create']) >= 3 ? categoriesCreate($form['cat_create'], 'files') : $form['cat'],
				'link_title' => $db->escape($form['link_title']),
				'text' => $db->escape($form['text'], 2),
			);
			if (is_array($new_file_sql)) {
				$old_file = $db->select('file', 'files', 'id = \'' . $uri->id . '\'');
				removeFile('files', $old_file[0]['file']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('files', $update_values, 'id = \'' . $uri->id . '\'');
			setFilesCache($uri->id);

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('acp/files'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$dl = $db->select('start, end, category_id, file, size, link_title, text', 'files', 'id = \'' . $uri->id . '\'');
		$dl[0]['text'] = $db->escape($dl[0]['text'], 3);
		// Datumsauswahl
		$tpl->assign('start_date', datepicker('start', $dl[0]['start']));
		$tpl->assign('end_date', datepicker('end', $dl[0]['end']));

		$unit = trim(strrchr($dl[0]['size'], ' '));

		$units[0]['value'] = 'Byte';
		$units[0]['selected'] = selectEntry('unit', 'Byte', $unit);
		$units[1]['value'] = 'KiB';
		$units[1]['selected'] = selectEntry('unit', 'KiB', $unit);
		$units[2]['value'] = 'MiB';
		$units[2]['selected'] = selectEntry('unit', 'MiB', $unit);
		$units[3]['value'] = 'GiB';
		$units[3]['selected'] = selectEntry('unit', 'GiB', $unit);
		$units[4]['value'] = 'TiB';
		$units[4]['selected'] = selectEntry('unit', 'TiB', $unit);
		$tpl->assign('units', $units);

		$dl[0]['filesize'] = substr($dl[0]['size'], 0, strpos($dl[0]['size'], ' '));

		// Formularelemente
		if (modules::check('categories', 'functions')) {
			include_once ACP3_ROOT . 'modules/categories/functions.php';
			$tpl->assign('categories', categoriesList('files', $dl[0]['category_id'], true));
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