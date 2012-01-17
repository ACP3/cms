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

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (!validate::isNumber($form['entries']))
		$errors[] = $lang->t('system', 'select_entries_per_page');
	if (!validate::isNumber($form['flood']))
		$errors[] = $lang->t('system', 'type_in_flood_barrier');
	if (!validate::isInternalURI($form['homepage']))
		$errors[] = $lang->t('system', 'incorrect_homepage');
	if ($form['wysiwyg'] != 'textarea' && (preg_match('=/=', $form['wysiwyg']) || is_file(INCLUDES_DIR . 'wysiwyg/' . $form['wysiwyg'] . '/info.xml') === false))
		$errors[] = $lang->t('system', 'select_editor');
	if (empty($form['date_format_long']) || empty($form['date_format_short']))
		$errors[] = $lang->t('system', 'type_in_date_format');
	if (!is_numeric($form['date_time_zone']))
		$errors[] = $lang->t('common', 'select_time_zone');
	if (!validate::isNumber($form['date_dst']))
		$errors[] = $lang->t('common', 'select_daylight_saving_time');
	if (!validate::isNumber($form['maintenance_mode']))
		$errors[] = $lang->t('system', 'select_online_maintenance');
	if (strlen($form['maintenance_message']) < 3)
		$errors[] = $lang->t('system', 'maintenance_message_to_short');
	if (empty($form['seo_title']))
		$errors[] = $lang->t('system', 'title_to_short');
	if (!validate::isNumber($form['seo_aliases']))
		$errors[] = $lang->t('system', 'select_seo_aliases');
	if (!validate::isNumber($form['seo_mod_rewrite']))
		$errors[] = $lang->t('system', 'select_mod_rewrite');
	if (!validate::isNumber($form['cache_images']))
		$errors[] = $lang->t('system', 'select_cache_images');
	if (!validate::isNumber($form['cache_minify']))
		$errors[] = $lang->t('system', 'type_in_minify_cache_lifetime');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		// Konfig aktualisieren
		$config = array(
			'cache_images' => (bool) $form['cache_images'],
			'cache_minify' => $form['cache_minify'],
			'date_dst' => $form['date_dst'],
			'date_format_long' => $db->escape($form['date_format_long']),
			'date_format_short' => $db->escape($form['date_format_short']),
			'date_time_zone' => $form['date_time_zone'],
			'entries' => $form['entries'],
			'flood' => $form['flood'],
			'homepage' => $form['homepage'],
			'maintenance_message' => $db->escape($form['maintenance_message']),
			'maintenance_mode' => (bool) $form['maintenance_mode'],
			'seo_aliases' => (bool) $form['seo_aliases'],
			'seo_meta_description' => $db->escape($form['seo_meta_description']),
			'seo_meta_keywords' => $db->escape($form['seo_meta_keywords']),
			'seo_mod_rewrite' => (bool) $form['seo_mod_rewrite'],
			'seo_title' => $db->escape($form['seo_title']),
			'wysiwyg' => $form['wysiwyg']
		);

		$bool = config::system($config);

		$content = comboBox($bool ? $lang->t('system', 'config_edit_success') : $lang->t('system', 'config_edit_error'), $uri->route('acp/system/configuration'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	// Einträge pro Seite
	$entries = array();
	for ($i = 0, $j = 10; $j <= 50; $i++, $j = $j + 10) {
		$entries[$i]['value'] = $j;
		$entries[$i]['selected'] = selectEntry('entries', $j, CONFIG_ENTRIES);
	}
	$tpl->assign('entries', $entries);

	// WYSIWYG-Editoren
	$editors = scandir(INCLUDES_DIR . 'wysiwyg');
	$c_editors = count($editors);
	$wysiwyg = array();

	for ($i = 0; $i < $c_editors; ++$i) {
		$info = xml::parseXmlFile(INCLUDES_DIR . 'wysiwyg/' . $editors[$i] . '/info.xml', '/editor');
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
	$tpl->assign('time_zone', timeZones(CONFIG_DATE_TIME_ZONE, 'date_time_zone'));

	// Sommerzeit an/aus
	$dst = array();
	$dst[0]['value'] = '1';
	$dst[0]['checked'] = selectEntry('date_dst', '1', CONFIG_DATE_DST, 'checked');
	$dst[0]['lang'] = $lang->t('common', 'yes');
	$dst[1]['value'] = '0';
	$dst[1]['checked'] = selectEntry('date_dst', '0', CONFIG_DATE_DST, 'checked');
	$dst[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('dst', $dst);

	// Wartungsmodus an/aus
	$maintenance = array();
	$maintenance[0]['value'] = '1';
	$maintenance[0]['checked'] = selectEntry('maintenance_mode', '1', CONFIG_MAINTENANCE_MODE, 'checked');
	$maintenance[0]['lang'] = $lang->t('common', 'yes');
	$maintenance[1]['value'] = '0';
	$maintenance[1]['checked'] = selectEntry('maintenance_mode', '0', CONFIG_MAINTENANCE_MODE, 'checked');
	$maintenance[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('maintenance', $maintenance);

	// Sef-URIs
	$mod_rewrite = array();
	$mod_rewrite[0]['value'] = '1';
	$mod_rewrite[0]['checked'] = selectEntry('seo_mod_rewrite', '1', CONFIG_SEO_MOD_REWRITE, 'checked');
	$mod_rewrite[0]['lang'] = $lang->t('common', 'yes');
	$mod_rewrite[1]['value'] = '0';
	$mod_rewrite[1]['checked'] = selectEntry('seo_mod_rewrite', '0', CONFIG_SEO_MOD_REWRITE, 'checked');
	$mod_rewrite[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('mod_rewrite', $mod_rewrite);

	// URI-Aliases aktivieren/deaktivieren
	$aliases = array();
	$aliases[0]['value'] = '1';
	$aliases[0]['checked'] = selectEntry('seo_aliases', '1', CONFIG_SEO_ALIASES, 'checked');
	$aliases[0]['lang'] = $lang->t('common', 'yes');
	$aliases[1]['value'] = '0';
	$aliases[1]['checked'] = selectEntry('seo_aliases', '0', CONFIG_SEO_ALIASES, 'checked');
	$aliases[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('aliases', $aliases);
	
	// Caching von Bildern
	$cache_images = array();
	$cache_images[0]['value'] = '1';
	$cache_images[0]['checked'] = selectEntry('cache_images', '1', CONFIG_CACHE_IMAGES, 'checked');
	$cache_images[0]['lang'] = $lang->t('common', 'yes');
	$cache_images[1]['value'] = '0';
	$cache_images[1]['checked'] = selectEntry('cache_images', '0', CONFIG_CACHE_IMAGES, 'checked');
	$cache_images[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('cache_images', $cache_images);

	$current = array(
		'cache_minify' => CONFIG_CACHE_MINIFY,
		'date_format_long' => CONFIG_DATE_FORMAT_LONG,
		'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
		'flood' => CONFIG_FLOOD,
		'homepage' => CONFIG_HOMEPAGE,
		'maintenance_message' => CONFIG_MAINTENANCE_MESSAGE,
		'seo_meta_description' => CONFIG_SEO_META_DESCRIPTION,
		'seo_meta_keywords' => CONFIG_SEO_META_KEYWORDS,
		'seo_title' => CONFIG_SEO_TITLE
	);

	$tpl->assign('form', isset($form) ? $form : $current);

	$content = modules::fetchTemplate('system/configuration.tpl');
}
