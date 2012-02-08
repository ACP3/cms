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

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (validate::isNumber($form['entries']) === false)
		$errors[] = $lang->t('system', 'select_entries_per_page');
	if (validate::isNumber($form['flood']) === false)
		$errors[] = $lang->t('system', 'type_in_flood_barrier');
	if (validate::isInternalURI($form['homepage']) === false)
		$errors[] = $lang->t('system', 'incorrect_homepage');
	if ($form['wysiwyg'] != 'textarea' && (preg_match('=/=', $form['wysiwyg']) || is_file(INCLUDES_DIR . 'wysiwyg/' . $form['wysiwyg'] . '/info.xml') === false))
		$errors[] = $lang->t('system', 'select_editor');
	if (empty($form['date_format_long']) || empty($form['date_format_short']))
		$errors[] = $lang->t('system', 'type_in_date_format');
	if (!is_numeric($form['date_time_zone']))
		$errors[] = $lang->t('common', 'select_time_zone');
	if (validate::isNumber($form['date_dst']) === false)
		$errors[] = $lang->t('common', 'select_daylight_saving_time');
	if (validate::isNumber($form['maintenance_mode']) === false)
		$errors[] = $lang->t('system', 'select_online_maintenance');
	if (strlen($form['maintenance_message']) < 3)
		$errors[] = $lang->t('system', 'maintenance_message_to_short');
	if (empty($form['seo_title']))
		$errors[] = $lang->t('system', 'title_to_short');
	if (validate::isNumber($form['seo_aliases']) === false)
		$errors[] = $lang->t('system', 'select_seo_aliases');
	if (validate::isNumber($form['seo_mod_rewrite']) === false)
		$errors[] = $lang->t('system', 'select_mod_rewrite');
	if (validate::isNumber($form['cache_images']) === false)
		$errors[] = $lang->t('system', 'select_cache_images');
	if (validate::isNumber($form['cache_minify']) === false)
		$errors[] = $lang->t('system', 'type_in_minify_cache_lifetime');
	if ($form['mailer_type'] === 'smtp') {
		if (empty($form['mailer_smtp_host']))
			$errors[] = $lang->t('system', 'type_in_mailer_smtp_host');
		if (validate::isNumber($form['mailer_smtp_port']) === false)
			$errors[] = $lang->t('system', 'type_in_mailer_smtp_port');
		if ($form['mailer_smtp_auth'] == 1 && empty($form['mailer_smtp_user']))
			$errors[] = $lang->t('system', 'type_in_mailer_smtp_username');
	}

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
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
			'mailer_smtp_auth' => (bool) $form['mailer_smtp_auth'],
			'mailer_smtp_host' => $form['mailer_smtp_host'],
			'mailer_smtp_password' => $form['mailer_smtp_password'],
			'mailer_smtp_port' => (int) $form['mailer_smtp_port'],
			'mailer_smtp_security' => $form['mailer_smtp_security'],
			'mailer_smtp_user' => $form['mailer_smtp_user'],
			'mailer_type' => $form['mailer_type'],
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

		$session->unsetFormToken();

		view::setContent(confirmBox($bool === true ? $lang->t('system', 'config_edit_success') : $lang->t('system', 'config_edit_error'), $uri->route('acp/system/configuration')));
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
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

	// Mailertyp
	$mailer_type = array();
	$mailer_type[0]['value'] = 'mail';
	$mailer_type[0]['selected'] = selectEntry('mailer_type', 'mail', CONFIG_MAILER_TYPE);
	$mailer_type[0]['lang'] = $lang->t('system', 'mailer_type_php_mail');
	$mailer_type[1]['value'] = 'smtp';
	$mailer_type[1]['selected'] = selectEntry('mailer_type', 'smtp', CONFIG_MAILER_TYPE);
	$mailer_type[1]['lang'] = $lang->t('system', 'mailer_type_smtp');
	$tpl->assign('mailer_type', $mailer_type);

	// Mailer SMTP Authentifizierung
	$mailer_smtp_auth = array();
	$mailer_smtp_auth[0]['value'] = '1';
	$mailer_smtp_auth[0]['checked'] = selectEntry('seo_aliases', '1', CONFIG_MAILER_SMTP_AUTH, 'checked');
	$mailer_smtp_auth[0]['lang'] = $lang->t('common', 'yes');
	$mailer_smtp_auth[1]['value'] = '0';
	$mailer_smtp_auth[1]['checked'] = selectEntry('seo_aliases', '0', CONFIG_MAILER_SMTP_AUTH, 'checked');
	$mailer_smtp_auth[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('mailer_smtp_auth', $mailer_smtp_auth);

	// Mailer SMTP Verschlüsselung
	$mailer_smtp_security = array();
	$mailer_smtp_security[0]['value'] = 'none';
	$mailer_smtp_security[0]['selected'] = selectEntry('mailer_smtp_security', '', CONFIG_MAILER_SMTP_SECURITY);
	$mailer_smtp_security[0]['lang'] = $lang->t('system', 'mailer_smtp_security_none');
	$mailer_smtp_security[1]['value'] = 'ssl';
	$mailer_smtp_security[1]['selected'] = selectEntry('mailer_smtp_security', 'ssl', CONFIG_MAILER_SMTP_SECURITY);
	$mailer_smtp_security[1]['lang'] = $lang->t('system', 'mailer_smtp_security_ssl');
	$mailer_smtp_security[2]['value'] = 'tls';
	$mailer_smtp_security[2]['selected'] = selectEntry('mailer_smtp_security', 'tls', CONFIG_MAILER_SMTP_SECURITY);
	$mailer_smtp_security[2]['lang'] = $lang->t('system', 'mailer_smtp_security_tls');
	$tpl->assign('mailer_smtp_security', $mailer_smtp_security);

	$current = array(
		'cache_minify' => CONFIG_CACHE_MINIFY,
		'date_format_long' => CONFIG_DATE_FORMAT_LONG,
		'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
		'flood' => CONFIG_FLOOD,
		'homepage' => CONFIG_HOMEPAGE,
		'mailer_smtp_host' => CONFIG_MAILER_SMTP_HOST,
		'mailer_smtp_password' => CONFIG_MAILER_SMTP_PASSWORD,
		'mailer_smtp_port' => CONFIG_MAILER_SMTP_PORT,
		'mailer_smtp_user' => CONFIG_MAILER_SMTP_USER,
		'maintenance_message' => CONFIG_MAINTENANCE_MESSAGE,
		'seo_meta_description' => CONFIG_SEO_META_DESCRIPTION,
		'seo_meta_keywords' => CONFIG_SEO_META_KEYWORDS,
		'seo_title' => CONFIG_SEO_TITLE
	);

	$tpl->assign('form', isset($form) ? $form : $current);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('system/configuration.tpl'));
}
