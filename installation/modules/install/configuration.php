<?php
if (!defined('IN_INSTALL'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/install/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Einträge pro Seite
	$i = 0;
	for ($j = 10; $j <= 50; $j = $j + 10) {
		$entries[$i]['value'] = $j;
		$entries[$i]['selected'] = select_entry('entries', $j, '20');
		$i++;
	}
	$tpl->assign('entries', $entries);

	// Sef-URIs
	$sef[0]['checked'] = select_entry('sef', '1', '0', 'checked');
	$sef[1]['checked'] = select_entry('sef', '0', '0', 'checked');
	$tpl->assign('sef', $sef);

	// Zeitzonen
	$time_zones = array(-12, -11, -10, -9.5, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 8, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 14);
	$check_dst = date('I');
	$offset = date('Z') - ($check_dst == '1' ? 3600 : 0);
	$i = 0;
	foreach ($time_zones as $row) {
		$time_zone[$i]['value'] = $row * 3600;
		$time_zone[$i]['selected'] = select_entry('time_zone', $row * 3600, $offset);
		$time_zone[$i]['lang'] = lang('utc' . $row);
		$i++;
	}
	$tpl->assign('time_zone', $time_zone);

	// Sommerzeit an/aus
	$dst[0]['checked'] = select_entry('dst', '1', $check_dst, 'checked');
	$dst[1]['checked'] = select_entry('dst', '0', $check_dst, 'checked');
	$tpl->assign('dst', $dst);

	$defaults['db_pre'] = 'acp3_';
	$defaults['user_name'] = 'admin';
	$defaults['flood'] = '30';
	$defaults['date'] = 'd.m.y, H:i';

	$tpl->assign('form', isset($form) ? $form : $defaults);

	$default_db_type = extension_loaded('mysqli') ? 'mysqli' : 'mysql';

	$db_type[0]['value'] = 'mysql';
	$db_type[0]['selected'] = select_entry('db_type', 'mysql', $default_db_type);
	$db_type[0]['lang'] = 'MySQL';
	if (extension_loaded('mysqli'))	{
		$db_type[1]['value'] = 'mysqli';
		$db_type[1]['selected'] = select_entry('db_type', 'mysqli', $default_db_type);
		$db_type[1]['lang'] = 'MySQLi';
	}
	$tpl->assign('db_type', $db_type);
}
$content = $tpl->fetch('configuration.html');
?>