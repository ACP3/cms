<?php
/**
 * System
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (ACP3_Validate::isNumber($_POST['entries']) === false)
		$errors['entries'] = $lang->t('common', 'select_records_per_page');
	if (ACP3_Validate::isNumber($_POST['flood']) === false)
		$errors['flood'] = $lang->t('system', 'type_in_flood_barrier');
	if (ACP3_Validate::isInternalURI($_POST['homepage']) === false)
		$errors['homepage'] = $lang->t('system', 'incorrect_homepage');
	if ($_POST['wysiwyg'] != 'textarea' && (preg_match('=/=', $_POST['wysiwyg']) || is_file(INCLUDES_DIR . 'wysiwyg/' . $_POST['wysiwyg'] . '/info.xml') === false))
		$errors['wysiwyg'] = $lang->t('system', 'select_editor');
	if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
		$errors[] = $lang->t('system', 'type_in_date_format');
	if (ACP3_Validate::timeZone($_POST['date_time_zone']) === false)
		$errors['date-time-zone'] = $lang->t('common', 'select_time_zone');
	if (ACP3_Validate::isNumber($_POST['maintenance_mode']) === false)
		$errors[] = $lang->t('system', 'select_online_maintenance');
	if (strlen($_POST['maintenance_message']) < 3)
		$errors['maintenance-message'] = $lang->t('system', 'maintenance_message_to_short');
	if (empty($_POST['seo_title']))
		$errors['seo-title'] = $lang->t('system', 'title_to_short');
	if (ACP3_Validate::isNumber($_POST['seo_robots']) === false)
		$errors[] = $lang->t('system', 'select_seo_robots');
	if (ACP3_Validate::isNumber($_POST['seo_aliases']) === false)
		$errors[] = $lang->t('system', 'select_seo_aliases');
	if (ACP3_Validate::isNumber($_POST['seo_mod_rewrite']) === false)
		$errors[] = $lang->t('system', 'select_mod_rewrite');
	if (ACP3_Validate::isNumber($_POST['cache_images']) === false)
		$errors[] = $lang->t('system', 'select_cache_images');
	if (ACP3_Validate::isNumber($_POST['cache_minify']) === false)
		$errors['cache-minify'] = $lang->t('system', 'type_in_minify_cache_lifetime');
	if (!empty($_POST['extra_css']) && ACP3_Validate::extraCSS($_POST['extra_css']) === false)
		$errors['extra-css'] = $lang->t('system', 'type_in_additional_stylesheets');
	if (!empty($_POST['extra_js']) && ACP3_Validate::extraJS($_POST['extra_js']) === false)
		$errors['extra-js'] = $lang->t('system', 'type_in_additional_javascript_files');
	if ($_POST['mailer_type'] === 'smtp') {
		if (empty($_POST['mailer_smtp_host']))
			$errors['mailer-smtp-host'] = $lang->t('system', 'type_in_mailer_smtp_host');
		if (ACP3_Validate::isNumber($_POST['mailer_smtp_port']) === false)
			$errors['mailer-smtp-port'] = $lang->t('system', 'type_in_mailer_smtp_port');
		if ($_POST['mailer_smtp_auth'] == 1 && empty($_POST['mailer_smtp_user']))
			$errors['mailer-smtp-username'] = $lang->t('system', 'type_in_mailer_smtp_username');
	}

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		// Config aktualisieren
		$config = array(
			'cache_images' => (int) $_POST['cache_images'],
			'cache_minify' => (int) $_POST['cache_minify'],
			'date_format_long' => $db->escape($_POST['date_format_long']),
			'date_format_short' => $db->escape($_POST['date_format_short']),
			'date_time_zone' => $_POST['date_time_zone'],
			'entries' => (int) $_POST['entries'],
			'extra_css' => $db->escape($_POST['extra_css'], 2),
			'extra_js' => $db->escape($_POST['extra_js'], 2),
			'flood' => (int) $_POST['flood'],
			'homepage' => $_POST['homepage'],
			'mailer_smtp_auth' => (int) $_POST['mailer_smtp_auth'],
			'mailer_smtp_host' => $_POST['mailer_smtp_host'],
			'mailer_smtp_password' => $_POST['mailer_smtp_password'],
			'mailer_smtp_port' => (int) $_POST['mailer_smtp_port'],
			'mailer_smtp_security' => $_POST['mailer_smtp_security'],
			'mailer_smtp_user' => $_POST['mailer_smtp_user'],
			'mailer_type' => $_POST['mailer_type'],
			'maintenance_message' => $db->escape($_POST['maintenance_message']),
			'maintenance_mode' => (int) $_POST['maintenance_mode'],
			'seo_aliases' => (int) $_POST['seo_aliases'],
			'seo_meta_description' => $db->escape($_POST['seo_meta_description']),
			'seo_meta_keywords' => $db->escape($_POST['seo_meta_keywords']),
			'seo_mod_rewrite' => (int) $_POST['seo_mod_rewrite'],
			'seo_robots' => (int) $_POST['seo_robots'],
			'seo_title' => $db->escape($_POST['seo_title']),
			'wysiwyg' => $_POST['wysiwyg']
		);

		$bool = ACP3_Config::setSettings('system', $config);

		// Gecachete Stylesheets und JavaScript Dateien löschen
		if (CONFIG_EXTRA_CSS !== $_POST['extra_css'] ||
			CONFIG_EXTRA_JS !== $_POST['extra_js']) {
			ACP3_Cache::purge('minify');
		}

		$session->unsetFormToken();

		ACP3_View::setContent(confirmBox($bool === true ? $lang->t('system', 'config_edit_success') : $lang->t('system', 'config_edit_error'), $uri->route('acp/system/configuration')));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('entries', recordsPerPage(CONFIG_ENTRIES));

	// WYSIWYG-Editoren
	$editors = scandir(INCLUDES_DIR . 'wysiwyg');
	$c_editors = count($editors);
	$wysiwyg = array();

	for ($i = 0; $i < $c_editors; ++$i) {
		$info = ACP3_XML::parseXmlFile(INCLUDES_DIR . 'wysiwyg/' . $editors[$i] . '/info.xml', '/editor');
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
	$tpl->assign('time_zones', $date->getTimeZones(CONFIG_DATE_TIME_ZONE));

	// Wartungsmodus an/aus
	$maintenance = array();
	$maintenance[0]['value'] = '1';
	$maintenance[0]['checked'] = selectEntry('maintenance_mode', '1', CONFIG_MAINTENANCE_MODE, 'checked');
	$maintenance[0]['lang'] = $lang->t('common', 'yes');
	$maintenance[1]['value'] = '0';
	$maintenance[1]['checked'] = selectEntry('maintenance_mode', '0', CONFIG_MAINTENANCE_MODE, 'checked');
	$maintenance[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('maintenance', $maintenance);

	// Robots
	$robots = array();
	$robots[0]['value'] = '1';
	$robots[0]['selected'] = selectEntry('seo_robots', '1', CONFIG_SEO_ROBOTS);
	$robots[0]['lang'] = $lang->t('common', 'seo_robots_index_follow');
	$robots[1]['value'] = '2';
	$robots[1]['selected'] = selectEntry('seo_robots', '2', CONFIG_SEO_ROBOTS);
	$robots[1]['lang'] = $lang->t('common', 'seo_robots_index_nofollow');
	$robots[2]['value'] = '3';
	$robots[2]['selected'] = selectEntry('seo_robots', '3', CONFIG_SEO_ROBOTS);
	$robots[2]['lang'] = $lang->t('common', 'seo_robots_noindex_follow');
	$robots[3]['value'] = '4';
	$robots[3]['selected'] = selectEntry('seo_robots', '4', CONFIG_SEO_ROBOTS);
	$robots[3]['lang'] = $lang->t('common', 'seo_robots_noindex_nofollow');
	$tpl->assign('robots', $robots);

	// URI-Aliases aktivieren/deaktivieren
	$aliases = array();
	$aliases[0]['value'] = '1';
	$aliases[0]['checked'] = selectEntry('seo_aliases', '1', CONFIG_SEO_ALIASES, 'checked');
	$aliases[0]['lang'] = $lang->t('common', 'yes');
	$aliases[1]['value'] = '0';
	$aliases[1]['checked'] = selectEntry('seo_aliases', '0', CONFIG_SEO_ALIASES, 'checked');
	$aliases[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('aliases', $aliases);

	// Sef-URIs
	$mod_rewrite = array();
	$mod_rewrite[0]['value'] = '1';
	$mod_rewrite[0]['checked'] = selectEntry('seo_mod_rewrite', '1', CONFIG_SEO_MOD_REWRITE, 'checked');
	$mod_rewrite[0]['lang'] = $lang->t('common', 'yes');
	$mod_rewrite[1]['value'] = '0';
	$mod_rewrite[1]['checked'] = selectEntry('seo_mod_rewrite', '0', CONFIG_SEO_MOD_REWRITE, 'checked');
	$mod_rewrite[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('mod_rewrite', $mod_rewrite);

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

	$settings = ACP3_Config::getSettings('system');

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('system/acp_configuration.tpl'));
}