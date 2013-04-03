<?php
if (defined('IN_ADM') === false)
	exit;

ACP3_CMS::$breadcrumb
->append(ACP3_CMS::$lang->t('system', 'acp_maintenance'), ACP3_CMS::$uri->route('acp/system/maintenance'))
->append(ACP3_CMS::$lang->t('system', 'acp_sql_import'));

if (isset($_POST['submit']) === true) {
	if (isset($_FILES['file'])) {
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];
	}

	if (empty($_POST['text']) && empty($file['size']))
		$errors['text'] = ACP3_CMS::$lang->t('system', 'type_in_text_or_select_sql_file');
	if (!empty($file['size']) &&
		(!ACP3_Validate::mimeType($file['tmp_name'], 'text/plain') ||
		$_FILES['file']['error'] !== UPLOAD_ERR_OK))
		$errors['file'] = ACP3_CMS::$lang->t('system', 'select_sql_file');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		ACP3_CMS::$session->unsetFormToken();

		$data = isset($file) ? file_get_contents($file['tmp_name']) : $_POST['text'];
		$data_ary = explode(";\n", str_replace(array("\r\n", "\r", "\n"), "\n", $data));
		$sql_queries = array();

		$i = 0;
		foreach ($data_ary as $row) {
			if (!empty($row)) {
				$bool = ACP3_CMS::$db2->query($row);
				$sql_queries[$i]['query'] = str_replace("\n", '<br />', $row);
				$sql_queries[$i]['color'] = $bool !== null ? '090' : 'f00';
				++$i;

				if (!$bool) {
					break;
				}
			}
		}

		ACP3_CMS::$view->assign('sql_queries', $sql_queries);

		ACP3_Cache::purge();
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('text' => ''));

	ACP3_CMS::$session->generateFormToken();
}