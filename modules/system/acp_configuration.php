<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/system/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Einträge pro Seite
	$i = 0;
	for ($j = 10; $j <= 50; $j = $j + 10) {
		$entries[$i]['value'] = $j;
		$entries[$i]['selected'] = select_entry('entries', $j, CONFIG_ENTRIES);
		$i++;
	}
	$tpl->assign('entries', $entries);

	// Sef-URIs
	$sef[0]['checked'] = select_entry('sef', '1', CONFIG_SEF, 'checked');
	$sef[1]['checked'] = select_entry('sef', '0', CONFIG_SEF, 'checked');
	$tpl->assign('sef', $sef);

	// Zeitzonen
	$tpl->assign('time_zone', time_zones(CONFIG_TIME_ZONE));

	// Sommerzeit an/aus
	$dst[0]['checked'] = select_entry('dst', '1', CONFIG_DST, 'checked');
	$dst[1]['checked'] = select_entry('dst', '0', CONFIG_DST, 'checked');
	$tpl->assign('dst', $dst);

	// Wartungsmodus an/aus
	$maintenance[0]['checked'] = select_entry('maintenance', '1', CONFIG_MAINTENANCE, 'checked');
	$maintenance[1]['checked'] = select_entry('maintenance', '0', CONFIG_MAINTENANCE, 'checked');
	$tpl->assign('maintenance', $maintenance);

	// Datenbank-Typen
	$db_type[0]['value'] = 'mysql';
	$db_type[0]['selected'] = select_entry('db_type', 'mysql', CONFIG_DB_TYPE);
	$db_type[0]['lang'] = 'MySQL';
	if (extension_loaded('mysqli'))	{
		$db_type[1]['value'] = 'mysqli';
		$db_type[1]['selected'] = select_entry('db_type', 'mysqli', CONFIG_DB_TYPE);
		$db_type[1]['lang'] = 'MySQLi';
	}
	$tpl->assign('db_type', $db_type);

	$current['flood'] = CONFIG_FLOOD;
	$current['date'] = CONFIG_DATE;
	$current['maintenance_msg'] = CONFIG_MAINTENANCE_MSG;
	$current['title'] = CONFIG_TITLE;
	$current['meta_description'] = CONFIG_META_DESCRIPTION;
	$current['meta_keywords'] = CONFIG_META_KEYWORDS;
	$current['db_host'] = CONFIG_DB_HOST;
	$current['db_user'] = CONFIG_DB_USER;
	$current['db_pwd'] = CONFIG_DB_PWD;
	$current['db_name'] = CONFIG_DB_NAME;
	$current['db_pre'] = CONFIG_DB_PRE;

	$tpl->assign('form', isset($form) ? $form : $current);

	$content = $tpl->fetch('system/acp_configuration.html');
}
?>