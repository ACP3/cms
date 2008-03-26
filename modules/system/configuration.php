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

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!$validate->isNumber($form['entries']))
		$errors[] = lang('system', 'select_entries_per_page');
	if (!$validate->isNumber($form['flood']))
		$errors[] = lang('system', 'type_in_flood_barrier');
	if (!$validate->isNumber($form['sef']))
		$errors[] = lang('system', 'select_sef_uris');
	if (empty($form['date']))
		$errors[] = lang('system', 'type_in_date_format');
	if (!is_numeric($form['time_zone']))
		$errors[] = lang('common', 'select_time_zone');
	if (!$validate->isNumber($form['dst']))
		$errors[] = lang('common', 'select_daylight_saving_time');
	if (!$validate->isNumber($form['maintenance']))
		$errors[] = lang('system', 'select_online_maintenance');
	if (strlen($form['maintenance_msg']) < 3)
		$errors[] = lang('system', 'maintenance_message_to_short');
	if (empty($form['title']))
		$errors[] = lang('system', 'title_to_short');
	if (empty($form['db_host']))
		$errors[] = lang('system', 'type_in_db_host');
	if (empty($form['db_user']))
		$errors[] = lang('system', 'type_in_db_username');
	if (empty($form['db_name']))
		$errors[] = lang('system', 'type_in_db_name');
	if (empty($form['db_type']))
		$errors[] = lang('system', 'select_db_type');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = $config->general($form);

		$content = comboBox($bool ? lang('system', 'config_edit_success') : lang('system', 'config_edit_error'), uri('acp/system/configuration'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Einträge pro Seite
	$i = 0;
	for ($j = 10; $j <= 50; $j = $j + 10) {
		$entries[$i]['value'] = $j;
		$entries[$i]['selected'] = selectEntry('entries', $j, CONFIG_ENTRIES);
		$i++;
	}
	$tpl->assign('entries', $entries);

	// Sef-URIs
	$sef[0]['checked'] = selectEntry('sef', '1', CONFIG_SEF, 'checked');
	$sef[1]['checked'] = selectEntry('sef', '0', CONFIG_SEF, 'checked');
	$tpl->assign('sef', $sef);

	// Zeitzonen
	$tpl->assign('time_zone', timeZones(CONFIG_TIME_ZONE));

	// Sommerzeit an/aus
	$dst[0]['checked'] = selectEntry('dst', '1', CONFIG_DST, 'checked');
	$dst[1]['checked'] = selectEntry('dst', '0', CONFIG_DST, 'checked');
	$tpl->assign('dst', $dst);

	// Wartungsmodus an/aus
	$maintenance[0]['checked'] = selectEntry('maintenance', '1', CONFIG_MAINTENANCE, 'checked');
	$maintenance[1]['checked'] = selectEntry('maintenance', '0', CONFIG_MAINTENANCE, 'checked');
	$tpl->assign('maintenance', $maintenance);

	// Datenbank-Typen
	$db_type[0]['value'] = 'mysql';
	$db_type[0]['selected'] = selectEntry('db_type', 'mysql', CONFIG_DB_TYPE);
	$db_type[0]['lang'] = 'MySQL';
	if (extension_loaded('mysqli'))	{
		$db_type[1]['value'] = 'mysqli';
		$db_type[1]['selected'] = selectEntry('db_type', 'mysqli', CONFIG_DB_TYPE);
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

	$content = $tpl->fetch('system/configuration.html');
}
?>