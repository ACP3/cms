<?php
if (!defined('IN_ADM'))
	exit;

breadcrumb::assign(lang('common', 'acp'), uri('acp'));
breadcrumb::assign(lang('system', 'system'), uri('acp/system'));
breadcrumb::assign(lang('system', 'maintenance'), uri('acp/system/maintenance'));
breadcrumb::assign(lang('system', 'sql_export'));

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (empty($form['tables']))
		$errors[] = lang('system', 'select_sql_tables');
	if ($form['output'] != 'file' && $form['output'] != 'text')
		$errors[] = lang('system', 'select_output');
	if ($form['export_type'] != 'complete' && $form['export_type'] != 'structure' && $form['export_type'] != 'data')
		$errors[] = lang('system', 'select_export_type');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$structure = '';
		$data = '';
		foreach ($form['tables'] as $table) {
			if ($form['export_type'] == 'complete' || $form['export_type'] == 'structure') {
				$result = $db->query('SHOW CREATE TABLE ' . $table);
				if (is_array($result)) {
					$structure.= '-- ' . sprintf(lang('system', 'structure_of_table'), $table) . "\n\n";
					$structure.= $result[0]['Create Table'] . ';' . "\n\n";
				}
			}
			if ($form['export_type'] == 'complete' || $form['export_type'] == 'data') {
				$resultsets = $db->select('*', substr($table, strlen(CONFIG_DB_PRE), strlen($table)));
				if (count($resultsets) > 0) {
					$data.= "\n" . '-- '. sprintf(lang('system', 'data_of_table'), $table) . "\n\n";
					$fields = '';
					foreach ($resultsets[0] as $field => $content) {
						$fields.= $field . ', ';
					}
					foreach ($resultsets as $row) {
						$values = '';
						foreach ($row as $value) {
							$values.= '\'' . $value . '\', ';
						}
						$data.= 'INSERT INTO ' . $table . ' (' . substr($fields, 0, -2) . ') VALUES (' . substr($values, 0, -2) . ');' . "\n";
					}
				}
			}
		}
		$export = $structure . $data;
		if ($form['output'] == 'file') {
			ob_end_clean();
			define('CUSTOM_CONTENT_TYPE', 'text/sql');
			header('Content-Disposition: attachment; filename=' . CONFIG_DB_NAME . '_export.sql');
			die($export);
		} else {
			$tpl->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
		}
	}
}
if (!isset($_POST['submit']) || isset($errors)) {
	$mod_list = modules::modulesList();
	$tables = array();

	foreach ($mod_list as $info) {
		if (is_array($info['tables'])) {
			foreach ($info['tables'] as $table) {
				$tables[$table]['name'] = CONFIG_DB_PRE . $table;
				$tables[$table]['selected'] = selectEntry('tables', $table);
			}
		}
	}
	ksort($tables);
	$tpl->assign('tables', $tables);

	// Ausgabe
	$output[0]['value'] = 'file';
	$output[0]['checked'] = selectEntry('output', 'file', 'file', 'checked');
	$output[0]['lang'] = lang('system', 'output_as_file');
	$output[1]['value'] = 'text';
	$output[1]['checked'] = selectEntry('output', 'text', 'file', 'checked');
	$output[1]['lang'] = lang('system', 'output_as_text');
	$tpl->assign('output', $output);

	// Exportart
	$export_type[0]['value'] = 'complete';
	$export_type[0]['checked'] = selectEntry('export_type', 'complete', 'complete', 'checked');
	$export_type[0]['lang'] = lang('system', 'complete_export');
	$export_type[1]['value'] = 'structure';
	$export_type[1]['checked'] = selectEntry('export_type', 'structure', 'complete', 'checked');
	$export_type[1]['lang'] = lang('system', 'export_structure');
	$export_type[2]['value'] = 'data';
	$export_type[2]['checked'] = selectEntry('export_type', 'data', 'complete', 'checked');
	$export_type[2]['lang'] = lang('system', 'export_data');
	$tpl->assign('export_type', $export_type);

}
$content = $tpl->fetch('system/sql_export.html');
?>