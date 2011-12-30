<?php
if (defined('IN_ADM') === false)
	exit;

breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
breadcrumb::assign($lang->t('system', 'system'), $uri->route('acp/system'));
breadcrumb::assign($lang->t('system', 'maintenance'), $uri->route('acp/system/maintenance'));
breadcrumb::assign($lang->t('system', 'sql_export'));

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (empty($form['tables']) || !is_array($form['tables']))
		$errors[] = $lang->t('system', 'select_sql_tables');
	if ($form['output'] != 'file' && $form['output'] != 'text')
		$errors[] = $lang->t('system', 'select_output');
	if ($form['export_type'] != 'complete' && $form['export_type'] != 'structure' && $form['export_type'] != 'data')
		$errors[] = $lang->t('system', 'select_export_type');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$structure = '';
		$data = '';
		foreach ($form['tables'] as $table) {
			// Struktur ausgeben
			if ($form['export_type'] == 'complete' || $form['export_type'] == 'structure') {
				$result = $db->query('SHOW CREATE TABLE ' . $table);
				if (is_array($result)) {
					//$structure.= '-- ' . sprintf($lang->t('system', 'structure_of_table'), $table) . "\n\n";
					$structure.= isset($form['drop']) && $form['drop'] == '1' ? 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n" : '';
					$structure.= $result[0]['Create Table'] . ';' . "\n\n";
				}
			}
			// Datensätze ausgeben
			if ($form['export_type'] == 'complete' || $form['export_type'] == 'data') {
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
		if ($form['output'] == 'file') {
			header('Content-Type: text/sql');
			header('Content-Disposition: attachment; filename=' . CONFIG_DB_NAME . '_export.sql');
			exit($export);
		// Im Browser ausgeben
		} else {
			$tpl->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
		}
	}
}
if (!isset($_POST['form']) || isset($errors)) {
	$mod_list = modules::modulesList();
	$tables = array();

	foreach ($mod_list as $info) {
		if (is_array($info['tables'])) {
			foreach ($info['tables'] as $table) {
				$tables[$table]['name'] = $db->prefix . $table;
				$tables[$table]['selected'] = selectEntry('tables', $table);
			}
		}
	}
	ksort($tables);
	$tpl->assign('tables', $tables);

	// Ausgabe
	$output[0]['value'] = 'file';
	$output[0]['checked'] = selectEntry('output', 'file', 'file', 'checked');
	$output[0]['lang'] = $lang->t('system', 'output_as_file');
	$output[1]['value'] = 'text';
	$output[1]['checked'] = selectEntry('output', 'text', '', 'checked');
	$output[1]['lang'] = $lang->t('system', 'output_as_text');
	$tpl->assign('output', $output);

	// Exportart
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

	$drop['checked'] = selectEntry('drop', '1', '', 'checked');
	$drop['lang'] = $lang->t('system', 'drop_tables');
	$tpl->assign('drop', $drop);
}
$content = modules::fetchTemplate('system/sql_export.html');
