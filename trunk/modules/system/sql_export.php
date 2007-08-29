<?php
if (!defined('IN_ADM'))
	exit;

$breadcrumb->assign(lang('system', 'system'), uri('acp/system'));
$breadcrumb->assign(lang('system', 'maintenance'), uri('acp/system/maintenance'));
$breadcrumb->assign(lang('system', 'sql_export'));

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

	$content = $tpl->fetch('system/sql_export.html');
}
?>