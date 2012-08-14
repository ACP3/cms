<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'files', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'categories/functions.php';

	$settings = ACP3_Config::getModuleSettings('files');

	if (isset($_POST['submit']) === true) {
		if (isset($_POST['external'])) {
			$file = $_POST['file_external'];
		} elseif (!empty($_FILES['file_internal']['name'])) {
			$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
			$file['name'] = $_FILES['file_internal']['name'];
			$file['size'] = $_FILES['file_internal']['size'];
		}

		if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($_POST['link_title']) < 3)
			$errors['link-title'] = $lang->t('files', 'type_in_link_title');
		if (isset($_POST['external']) && (empty($file) || empty($_POST['filesize']) || empty($_POST['unit'])))
			$errors['external'] = $lang->t('files', 'type_in_external_resource');
		if (!isset($_POST['external']) && isset($file) && is_array($file) &&
			(empty($file['tmp_name']) || empty($file['size']) || $_FILES['file_internal']['error'] !== UPLOAD_ERR_OK))
			$errors['file-internal'] = $lang->t('files', 'select_internal_resource');
		if (strlen($_POST['text']) < 3)
			$errors['text'] = $lang->t('files', 'description_to_short');
		if (strlen($_POST['cat_create']) < 3 && categoriesCheck($_POST['cat']) === false)
			$errors['cat'] = $lang->t('files', 'select_category');
		if (strlen($_POST['cat_create']) >= 3 && categoriesCheckDuplicate($_POST['cat_create'], 'files') === true)
			$errors['cat-create'] = $lang->t('categories', 'category_already_exists');
		if (CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], 'files/details/id_' . $uri->id) === true))
			$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			// Falls eine neue Datei angegeben wurde, Änderungen durchführen
			if (isset($file)) {
				if (is_array($file) === true) {
					$result = moveFile($file['tmp_name'], $file['name'], 'files');
					$new_file = $result['name'];
					$filesize = $result['size'];
				} else {
					$_POST['filesize'] = (float) $_POST['filesize'];
					$new_file = $file;
					$filesize = $_POST['filesize'] . ' ' . $db->escape($_POST['unit']);
				}
				// SQL Query für die Änderungen
				$new_file_sql = array(
					'file' => $new_file,
					'size' => $filesize,
				);
			}

			$update_values = array(
				'start' => $date->timestamp($_POST['start']),
				'end' => $date->timestamp($_POST['end']),
				'category_id' => strlen($_POST['cat_create']) >= 3 ? categoriesCreate($_POST['cat_create'], 'files') : $_POST['cat'],
				'link_title' => $db->escape($_POST['link_title']),
				'text' => $db->escape($_POST['text'], 2),
				'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
				'user_id' => $auth->getUserId(),
			);
			if (is_array($new_file_sql) === true) {
				$old_file = $db->select('file', 'files', 'id = \'' . $uri->id . '\'');
				removeUploadedFile('files', $old_file[0]['file']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('files', $update_values, 'id = \'' . $uri->id . '\'');
			if (CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
				ACP3_SEO::insertUriAlias('files/details/id_' . $uri->id, $_POST['alias'], $db->escape($_POST['seo_keywords']), $db->escape($_POST['seo_description']), (int) $_POST['seo_robots']);

			require_once MODULES_DIR . 'files/functions.php';
			setFilesCache($uri->id);

			$session->unsetFormToken();

			setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/files');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$dl = $db->select('start, end, category_id, file, size, link_title, text, comments', 'files', 'id = \'' . $uri->id . '\'');
		$dl[0]['text'] = $db->escape($dl[0]['text'], 3);

		// Datumsauswahl
		$tpl->assign('publication_period', $date->datepicker(array('start', 'end'), array($dl[0]['start'], $dl[0]['end'])));

		$unit = trim(strrchr($dl[0]['size'], ' '));

		$units = array();
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
		$tpl->assign('categories', categoriesList('files', $dl[0]['category_id'], true));

		if (ACP3_Modules::check('comments', 'functions') === true && $settings['comments'] == 1) {
			$options = array();
			$options[0]['name'] = 'comments';
			$options[0]['checked'] = selectEntry('comments', '1', $dl[0]['comments'], 'checked');
			$options[0]['lang'] = $lang->t('common', 'allow_comments');
			$tpl->assign('options', $options);
		}

		$tpl->assign('checked_external', isset($_POST['external']) ? ' checked="checked"' : '');
		$tpl->assign('current_file', $dl[0]['file']);

		$tpl->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields('files/details/id_' . $uri->id));
		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $dl[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('files/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/403');
}
