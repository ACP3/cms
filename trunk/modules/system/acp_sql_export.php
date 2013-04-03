<?php
if (defined('IN_ADM') === false)
	exit;

ACP3_CMS::$breadcrumb
->append(ACP3_CMS::$lang->t('system', 'acp_maintenance'), ACP3_CMS::$uri->route('acp/system/maintenance'))
->append(ACP3_CMS::$lang->t('system', 'acp_sql_export'));

if (isset($_POST['submit']) === true) {
	if (empty($_POST['tables']) || is_array($_POST['tables']) === false)
		$errors['tables'] = ACP3_CMS::$lang->t('system', 'select_sql_tables');
	if ($_POST['output'] !== 'file' && $_POST['output'] !== 'text')
		$errors[] = ACP3_CMS::$lang->t('system', 'select_output');
	if (in_array($_POST['export_type'], array('complete', 'structure', 'data')) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_export_type');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		ACP3_CMS::$session->unsetFormToken();

		$structure = '';
		$data = '';
		foreach ($_POST['tables'] as $table) {
			// Struktur ausgeben
			if ($_POST['export_type'] === 'complete' || $_POST['export_type'] === 'structure') {
				$result = ACP3_CMS::$db2->fetchAssoc('SHOW CREATE TABLE ' . $table);
				if (!empty($result)) {
					//$structure.= '-- ' . sprintf(ACP3_CMS::$lang->t('system', 'structure_of_table'), $table) . "\n\n";
					$structure.= isset($_POST['drop']) && $_POST['drop'] == 1 ? 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n" : '';
					$structure.= $result['Create Table'] . ';' . "\n\n";
				}
			}

			// Datensätze ausgeben
			if ($_POST['export_type'] === 'complete' || $_POST['export_type'] === 'data') {
				$resultsets = ACP3_CMS::$db2->fetchAll('SELECT * FROM ' . DB_PRE . substr($table, strlen(CONFIG_DB_PRE)));
				if (count($resultsets) > 0) {
					//$data.= "\n" . '-- '. sprintf(ACP3_CMS::$lang->t('system', 'data_of_table'), $table) . "\n\n";
					$fields = '';
					// Felder der jeweiligen Tabelle auslesen
					foreach (array_keys($resultsets[0]) as $field) {
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
		if ($_POST['output'] === 'file') {
			header('Content-Type: text/sql');
			header('Content-Disposition: attachment; filename=' . CONFIG_DB_NAME . '_export.sql');
			exit($export);
		// Im Browser ausgeben
		} else {
			ACP3_CMS::$view->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
		}
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$db_tables = ACP3_CMS::$db2->fetchAll('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_TYPE = ? AND TABLE_SCHEMA = ?', array('BASE TABLE', CONFIG_DB_NAME));
	$tables = array();
	foreach ($db_tables as $row) {
		$table = $row['TABLE_NAME'];
		if (strpos($table, CONFIG_DB_PRE) === 0) {
			$tables[$table]['name'] = $table;
			$tables[$table]['selected'] = selectEntry('tables', $table);
		}
	}
	ksort($tables);
	ACP3_CMS::$view->assign('tables', $tables);

	// Ausgabe
	$output = array();
	$output[0]['value'] = 'file';
	$output[0]['checked'] = selectEntry('output', 'file', 'file', 'checked');
	$output[0]['lang'] = ACP3_CMS::$lang->t('system', 'output_as_file');
	$output[1]['value'] = 'text';
	$output[1]['checked'] = selectEntry('output', 'text', '', 'checked');
	$output[1]['lang'] = ACP3_CMS::$lang->t('system', 'output_as_text');
	ACP3_CMS::$view->assign('output', $output);

	// Exportart
	$export_type = array();
	$export_type[0]['value'] = 'complete';
	$export_type[0]['checked'] = selectEntry('export_type', 'complete', 'complete', 'checked');
	$export_type[0]['lang'] = ACP3_CMS::$lang->t('system', 'complete_export');
	$export_type[1]['value'] = 'structure';
	$export_type[1]['checked'] = selectEntry('export_type', 'structure', '', 'checked');
	$export_type[1]['lang'] = ACP3_CMS::$lang->t('system', 'export_structure');
	$export_type[2]['value'] = 'data';
	$export_type[2]['checked'] = selectEntry('export_type', 'data', '', 'checked');
	$export_type[2]['lang'] = ACP3_CMS::$lang->t('system', 'export_data');
	ACP3_CMS::$view->assign('export_type', $export_type);

	$drop = array();
	$drop['checked'] = selectEntry('drop', '1', '', 'checked');
	$drop['lang'] = ACP3_CMS::$lang->t('system', 'drop_tables');
	ACP3_CMS::$view->assign('drop', $drop);

	ACP3_CMS::$session->generateFormToken();
}