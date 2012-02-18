<?php
if (defined('IN_ADM') === false)
	exit;

$breadcrumb->assign($lang->t('system', 'maintenance'), $uri->route('acp/system/maintenance'))
		   ->assign($lang->t('system', 'sql_export'));

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (empty($form['tables']) || is_array($form['tables']) === false)
		$errors['tables'] = $lang->t('system', 'select_sql_tables');
	if ($form['output'] !== 'file' && $form['output'] !== 'text')
		$errors[] = $lang->t('system', 'select_output');
	if (in_array($form['export_type'], array('complete', 'structure', 'data')) === false)
		$errors[] = $lang->t('system', 'select_export_type');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$session->unsetFormToken();

		$structure = '';
		$data = '';
		foreach ($form['tables'] as $table) {
			// Struktur ausgeben
			if ($form['export_type'] === 'complete' || $form['export_type'] === 'structure') {
				$result = $db->query('SHOW CREATE TABLE ' . $table);
				if (is_array($result) === true) {
					//$structure.= '-- ' . sprintf($lang->t('system', 'structure_of_table'), $table) . "\n\n";
					$structure.= isset($form['drop']) && $form['drop'] == 1 ? 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n" : '';
					$structure.= $result[0]['Create Table'] . ';' . "\n\n";
				}
			}
			// Datensätze ausgeben
			if ($form['export_type'] === 'complete' || $form['export_type'] === 'data') {
				$resultsets = $db->select('*', substr($table, strlen($db->prefix)));
				if (count($resultsets) > 0) {
					//$data.= "\n" . '-- '. sprintf($lang->t('system', 'data_of_table'), $table) . "\n\n";
					$fields = '';
					// Felder der jeweiligen Tabelle auslesen
					foreach ($resultsets[0] as $field => $content) {
						$fields.= '`' . $field . '`, ';
					}

					// Datensätze auslesen
					foreach ($resultsets as $row) {
						$values = '';
						foreach ($row as $value) {
							$values.= '\'' . $value . '\', ';
						}
						$data.= 'INSERT INTO `' . $table . '` (' . substr($fields, 0, -2) . ') VALUES (' . substr($values, 0, -2) . ');' . "\n";
					}
				}
			}
		}
		$export = $structure . $data;

		// Als Datei ausgeben
		if ($form['output'] === 'file') {
			header('Content-Type: text/sql');
			header('Content-Disposition: attachment; filename=' . CONFIG_DB_NAME . '_export.sql');
			exit($export);
		// Im Browser ausgeben
		} else {
			$tpl->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
		}
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$mod_list = modules::modulesList();
	$tables = array();

	foreach ($mod_list as $info) {
		if (is_array($info['tables']) === true) {
			foreach ($info['tables'] as $table) {
				$tables[$table]['name'] = $db->prefix . $table;
				$tables[$table]['selected'] = selectEntry('tables', $table);
			}
		}
	}
	ksort($tables);
	$tpl->assign('tables', $tables);

	// Ausgabe
	$output = array();
	$output[0]['value'] = 'file';
	$output[0]['checked'] = selectEntry('output', 'file', 'file', 'checked');
	$output[0]['lang'] = $lang->t('system', 'output_as_file');
	$output[1]['value'] = 'text';
	$output[1]['checked'] = selectEntry('output', 'text', '', 'checked');
	$output[1]['lang'] = $lang->t('system', 'output_as_text');
	$tpl->assign('output', $output);

	// Exportart
	$export_type = array();
	$export_type[0]['value'] = 'complete';
	$export_type[0]['checked'] = selectEntry('export_type', 'complete', 'complete', 'checked');
	$export_type[0]['lang'] = $lang->t('system', 'complete_export');
	$export_type[1]['value'] = 'structure';
	$export_type[1]['checked'] = selectEntry('export_type', 'structure', '', 'checked');
	$export_type[1]['lang'] = $lang->t('system', 'export_structure');
	$export_type[2]['value'] = 'data';
	$export_type[2]['checked'] = selectEntry('export_type', 'data', '', 'checked');
	$export_type[2]['lang'] = $lang->t('system', 'export_data');
	$tpl->assign('export_type', $export_type);

	$drop = array();
	$drop['checked'] = selectEntry('drop', '1', '', 'checked');
	$drop['lang'] = $lang->t('system', 'drop_tables');
	$tpl->assign('drop', $drop);

	$session->generateFormToken();
}
view::setContent(view::fetchTemplate('system/sql_export.tpl'));