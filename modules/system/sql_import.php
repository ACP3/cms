<?php
if (defined('IN_ADM') === false)
	exit;

breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
breadcrumb::assign($lang->t('system', 'system'), $uri->route('acp/system'));
breadcrumb::assign($lang->t('system', 'maintenance'), $uri->route('acp/system/maintenance'));
breadcrumb::assign($lang->t('system', 'sql_import'));

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];
	if (isset($_FILES['file'])) {
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];
	}

	if (empty($form['text']) && empty($file['size']))
		$errors[] = $lang->t('system', 'type_in_text_or_select_sql_file');
	if (!empty($file['size']) &&
		(!validate::mimeType($file['tmp_name'], 'text/plain') ||
		$_FILES['file']['error'] !== UPLOAD_ERR_OK))
		$errors[] = $lang->t('system', 'select_sql_file');
	if (!validate::formToken())
		$errors[] = $lang->t('common', 'form_already_submitted');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$session->unsetFormToken();

		$data = isset($file) ? file_get_contents($file['tmp_name']) : $form['text'];
		$data = str_replace(array("\r\n", "\r", "\n"), "\n", $data);
		$data_ary = explode(";\n", $data);
		$sql_queries = array();

		$i = 0;
		foreach ($data_ary as $row) {
			if (!empty($row)) {
				$bool = $db->query($row, 3);
				$sql_queries[$i]['query'] = str_replace("\n", '<br />', $row);
				$sql_queries[$i]['color'] = $bool !== null ? '090' : 'f00';
				++$i;

				if (!$bool) {
					break;
				}
			}
		}

		$tpl->assign('sql_queries', $sql_queries);

		cache::purge();
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($form) ? $form : array('text' => ''));

	$session->generateFormToken();
}
view::setContent(view::fetchTemplate('system/sql_import.tpl'));