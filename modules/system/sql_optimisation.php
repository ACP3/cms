<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$breadcrumb->append($lang->t('system', 'maintenance'), $uri->route('acp/system/maintenance'))
		   ->append($lang->t('system', 'sql_optimisation'));

if ($uri->action === 'do') {
	$mod_list = ACP3_Modules::getAllModules();
	$tables = array();
	$total_overhead = 0;
	$i = 0;

	foreach ($mod_list as $name => $info) {
		if (is_array($info['tables']) === true) {
			foreach ($info['tables'] as $table) {
				$table_status = $db->query('SHOW TABLE STATUS FROM ' . CONFIG_DB_NAME . ' LIKE \'{pre}' . $table . '\'');
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

ACP3_View::setContent(ACP3_View::fetchTemplate('system/sql_optimisation.tpl'));
