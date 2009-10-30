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

breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
breadcrumb::assign($lang->t('system', 'system'), uri('acp/system'));
breadcrumb::assign($lang->t('system', 'maintenance'), uri('acp/system/maintenance'));
breadcrumb::assign($lang->t('system', 'sql_optimisation'));

if ($uri->action == 'do') {
	$mod_list = modules::modulesList();
	$tables = array();
	$total_overhead = 0;
	$i = 0;

	foreach ($mod_list as $name => $info) {
		if (is_array($info['tables'])) {
			foreach ($info['tables'] as $table) {
				$table_status = $db->query('SHOW TABLE STATUS FROM ' . CONFIG_DB_NAME . ' LIKE \'' . $db->prefix . $table . '\'');
				$c_table_status = count($table_status);
				for ($j = 0; $j < $c_table_status; ++$j) {
					$tables[$i]['name'] = $table_status[$j]['Name'];
					if ($table_status[$j]['Data_free'] != 0) {
						$db->query('OPTIMIZE TABLE ' . $table_status[$j]['Name'], 3);

						$overhead = $table_status[$j]['Data_free'];
						$total_overhead+= $overhead;

						$tables[$i]['overhead'] = calcFilesize($overhead);
						$tables[$i]['status'] = $lang->t('system', 'optimised');
					} else {
						$tables[$i]['overhead'] = calcFilesize(0);
						$tables[$i]['status'] = $lang->t('system', 'not_optimised');
					}
					$i++;
				}
			}
		}
	}
	$tpl->assign('tables', $tables);
	$tpl->assign('total_overhead', calcFilesize($total_overhead));
}

$content = $tpl->fetch('system/sql_optimisation.html');
