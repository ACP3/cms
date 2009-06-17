<?php
if (!defined('IN_ADM'))
	exit;

breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
breadcrumb::assign($lang->t('system', 'system'), uri('acp/system'));
breadcrumb::assign($lang->t('system', 'maintenance'), uri('acp/system/maintenance'));
breadcrumb::assign($lang->t('system', 'sql_import'));

if (isset($_POST['submit'])) {
	$form = $_POST['form'];
	if (isset($_FILES['file'])) {
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];
	}

	if (empty($form['text']) && empty($file['size']))
		$errors[] = $lang->t('system', 'type_in_text_or_select_sql_file');
	if (!empty($file['size']) && !validate::mimeType($file['tmp_name'], 'text/plain'))
		$errors[] = $lang->t('system', 'select_sql_file');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$content = isset($file) ? file_get_contents($file['tmp_name']) : $form['text'];
		$content = str_replace(array("\r\n", "\r", "\n"), "\n", $content);
		$content_ary = explode(";\n", $content);
		$sql_queries = array();

		$i = 0;
		foreach ($content_ary as $row) {
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
if (!isset($_POST['submit']) || isset($errors)) {
	$tpl->assign('form', isset($form) ? $form : array('text' => ''));
}
$content = $tpl->fetch('system/sql_import.html');
?>