<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$breadcrumb->assign(lang('common', 'acp'), uri('acp'));
$breadcrumb->assign(lang('system', 'system'), uri('acp/system'));
$breadcrumb->assign(lang('system', 'maintenance'), uri('acp/system/maintenance'));
$breadcrumb->assign(lang('system', 'sql_optimisation'));

$action = $modules->action == 'do' ? true : false;

$tpl->assign('action', $action);

if ($action) {
	$overall_overhead = 0;
	$table_status = $db->query('SHOW TABLE STATUS FROM ' . CONFIG_DB_NAME);
	$c_table_status = count($table_status);

	for($i = 0; $i < $c_table_status; $i++) {
		$overhead_row = round($table_status[$i]['Data_free'] / 1024, 3);
		$overall_overhead = $overall_overhead + $overhead_row;

		if ($overhead_row == 0) {
			$table_status[$i]['status'] = lang('system', 'not_optimised');
			$table_status[$i]['overhead'] = 0;
		} else {
			$db->query('OPTIMIZE TABLE ' . $table_status[$i]['Name'], 3);
			$table_status[$i]['status'] = lang('system', 'optimised');
			$table_status[$i]['overhead'] = $overhead_row;
		}
	}
	$tpl->assign('table_status', $table_status);
	$tpl->assign('overall_overhead', $overall_overhead);
}

$content = $tpl->fetch('system/sql_optimisation.html');
?>