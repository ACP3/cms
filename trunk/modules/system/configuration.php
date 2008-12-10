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

	if (!validate::isNumber($form['entries']))
		$errors[] = $lang->t('system', 'select_entries_per_page');
	if (!validate::isNumber($form['flood']))
		$errors[] = $lang->t('system', 'type_in_flood_barrier');
	if (!preg_match('/^(\w+\/)+$/', $form['homepage']))
		$errors[] = $lang->t('system', 'incorrect_homepage');
	if ($form['wysiwyg'] != 'textarea' && (preg_match('=/=', $form['wysiwyg']) || !is_file(ACP3_ROOT . 'includes/wysiwyg/' . $form['wysiwyg'] . '/info.xml')))
		$errors[] = $lang->t('system', 'select_editor');
	if (empty($form['date']))
		$errors[] = $lang->t('system', 'type_in_date_format');
	if (!is_numeric($form['time_zone']))
		$errors[] = $lang->t('common', 'select_time_zone');
	if (!validate::isNumber($form['dst']))
		$errors[] = $lang->t('common', 'select_daylight_saving_time');
	if (!validate::isNumber($form['maintenance']))
		$errors[] = $lang->t('system', 'select_online_maintenance');
	if (strlen($form['maintenance_msg']) < 3)
		$errors[] = $lang->t('system', 'maintenance_message_to_short');
	if (empty($form['title']))
		$errors[] = $lang->t('system', 'title_to_short');
	if (!validate::isNumber($form['sef']))
		$errors[] = $lang->t('system', 'enable_disable_mod_rewrite');
	if (empty($form['db_host']))
		$errors[] = $lang->t('system', 'type_in_db_host');
	if (empty($form['db_user']))
		$errors[] = $lang->t('system', 'type_in_db_username');
	if (empty($form['db_name']))
		$errors[] = $lang->t('system', 'type_in_db_name');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		// Konfig aktualisieren
		$config = array(
			'date' => $db->escape($form['date']),
			'db_host' => $form['db_host'],
			'db_name' => $form['db_name'],
			'db_pre' => $db->escape($form['db_pre']),
			'db_pwd' => $form['db_pwd'],
			'db_user' => $form['db_user'],
			'design' => CONFIG_DESIGN,
			'dst' => $form['dst'],
			'entries' => $form['entries'],
			'flood' => $form['flood'],
			'homepage' => $form['homepage'],
			'lang' => CONFIG_LANG,
			'maintenance' => $form['maintenance'],
			'maintenance_msg' => $db->escape($form['maintenance_msg']),
			'meta_description' => $db->escape($form['meta_description']),
			'meta_keywords' => $db->escape($form['meta_keywords']),
			'sef' => $form['sef'],
			'time_zone' => $form['time_zone'],
			'title' => $db->escape($form['title']),
			'version' => CONFIG_VERSION,
			'wysiwyg' => $form['wysiwyg']
		);

		$bool = config::system($config);

		$content = comboBox($bool ? $lang->t('system', 'config_edit_success') : $lang->t('system', 'config_edit_error'), uri('acp/system/configuration'));
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

	// WYSIWYG-Editoren
	$editors = scandir(ACP3_ROOT . 'includes/wysiwyg');
	$c_editors = count($editors);
	$wysiwyg = array();

	for ($i = 0; $i < $c_editors; ++$i) {
		$info = xml::parseXmlFile(ACP3_ROOT . 'includes/wysiwyg/' . $editors[$i] . '/info.xml', '/editor');
		if (!empty($info)) {
			$wysiwyg[$i]['value'] = $editors[$i];
			$wysiwyg[$i]['selected'] = selectEntry('wysiwyg', $editors[$i], CONFIG_WYSIWYG);
			$wysiwyg[$i]['lang'] = $info['name'] . ' ' . $info['version'];
		}
	}
	// Normale <textarea>
	$wysiwyg[$i]['value'] = 'textarea';
	$wysiwyg[$i]['selected'] = selectEntry('wysiwyg', 'textarea', CONFIG_WYSIWYG);
	$wysiwyg[$i]['lang'] = $lang->t('system', 'textarea');
	$tpl->assign('wysiwyg', $wysiwyg);

	// Zeitzonen
	$tpl->assign('time_zone', timeZones(CONFIG_TIME_ZONE));

	// Sommerzeit an/aus
	$dst[0]['value'] = '1';
	$dst[0]['checked'] = selectEntry('dst', '1', CONFIG_DST, 'checked');
	$dst[0]['lang'] = $lang->t('common', 'yes');
	$dst[1]['value'] = '0';
	$dst[1]['checked'] = selectEntry('dst', '0', CONFIG_DST, 'checked');
	$dst[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('dst', $dst);

	// Wartungsmodus an/aus
	$maintenance[0]['value'] = '1';
	$maintenance[0]['checked'] = selectEntry('maintenance', '1', CONFIG_MAINTENANCE, 'checked');
	$maintenance[0]['lang'] = $lang->t('common', 'yes');
	$maintenance[1]['value'] = '0';
	$maintenance[1]['checked'] = selectEntry('maintenance', '0', CONFIG_MAINTENANCE, 'checked');
	$maintenance[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('maintenance', $maintenance);

	// Sef-URIs
	$sef[0]['value'] = '1';
	$sef[0]['checked'] = selectEntry('sef', '1', CONFIG_SEF, 'checked');
	$sef[0]['lang'] = $lang->t('common', 'yes');
	$sef[1]['value'] = '0';
	$sef[1]['checked'] = selectEntry('sef', '0', CONFIG_SEF, 'checked');
	$sef[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('sef', $sef);

	$current['flood'] = CONFIG_FLOOD;
	$current['homepage'] = CONFIG_HOMEPAGE;
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