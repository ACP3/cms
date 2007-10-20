<?php
if (!defined('IN_ACP'))
	exit;

$breadcrumb->assign(lang('system', 'system'), uri('system/acp_list'));
$breadcrumb->assign(lang('system', 'acp_maintenance'), uri('system/acp_maintenance'));
$breadcrumb->assign(lang('system', 'acp_sql_export'));

if (isset($_POST['submit'])) {
	include 'modules/system/entry.php';
}
if (!isset($_POST['submit']) || isset($errors)) {
	$mod_list = $modules->modulesList();
	$tables = array();

	foreach ($mod_list as $info) {
		if (is_array($info['tables'])) {
			foreach ($info['tables'] as $table) {
				$tables[$table]['name'] = CONFIG_DB_PRE . $table;
				$tables[$table]['selected'] = select_entry('tables', $table);
			}
		}
	}
	ksort($tables);
	$tpl->assign('tables', $tables);

	// Ausgabe
	$output[0]['selected'] = select_entry('output', 'file', 'file', 'checked');
	$output[1]['selected'] = select_entry('output', 'text', 'file', 'checked');
	$tpl->assign('output', $output);

	// Exportart
	$export_type[0]['selected'] = select_entry('export_type', 'complete', 'complete', 'checked');
	$export_type[1]['selected'] = select_entry('export_type', 'structure', 'complete', 'checked');
	$export_type[2]['selected'] = select_entry('export_type', 'data', 'complete', 'checked');
	$tpl->assign('export_type', $export_type);

}
$content = $tpl->fetch('system/acp_sql_export.html');
?>