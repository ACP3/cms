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
	if (ACP3_Validate::isInternalURI($_POST['homepage']) === false)
		$errors['homepage'] = ACP3_CMS::$lang->t('system', 'incorrect_homepage');
	if (ACP3_Validate::isNumber($_POST['entries']) === false)
		$errors['entries'] = ACP3_CMS::$lang->t('system', 'select_records_per_page');
	if (ACP3_Validate::isNumber($_POST['flood']) === false)
		$errors['flood'] = ACP3_CMS::$lang->t('system', 'type_in_flood_barrier');
	if ((bool) preg_match('/\/$/', $_POST['icons_path']) === false)
		$errors['icons-path'] = ACP3_CMS::$lang->t('system', 'incorrect_path_to_icons');
	if ($_POST['wysiwyg'] != 'textarea' && (preg_match('=/=', $_POST['wysiwyg']) || is_file(INCLUDES_DIR . 'wysiwyg/' . $_POST['wysiwyg'] . '/info.xml') === false))
		$errors['wysiwyg'] = ACP3_CMS::$lang->t('system', 'select_editor');
	if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
		$errors[] = ACP3_CMS::$lang->t('system', 'type_in_date_format');
	if (ACP3_Validate::timeZone($_POST['date_time_zone']) === false)
		$errors['date-time-zone'] = ACP3_CMS::$lang->t('system', 'select_time_zone');
	if (ACP3_Validate::isNumber($_POST['maintenance_mode']) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_online_maintenance');
	if (strlen($_POST['maintenance_message']) < 3)
		$errors['maintenance-message'] = ACP3_CMS::$lang->t('system', 'maintenance_message_to_short');
	if (empty($_POST['seo_title']))
		$errors['seo-title'] = ACP3_CMS::$lang->t('system', 'title_to_short');
	if (ACP3_Validate::isNumber($_POST['seo_robots']) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_seo_robots');
	if (ACP3_Validate::isNumber($_POST['seo_aliases']) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_seo_aliases');
	if (ACP3_Validate::isNumber($_POST['seo_mod_rewrite']) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_mod_rewrite');
	if (ACP3_Validate::isNumber($_POST['cache_images']) === false)
		$errors[] = ACP3_CMS::$lang->t('system', 'select_cache_images');
	if (ACP3_Validate::isNumber($_POST['cache_minify']) === false)
		$errors['cache-minify'] = ACP3_CMS::$lang->t('system', 'type_in_minify_cache_lifetime');
	if (!empty($_POST['extra_css']) && ACP3_Validate::extraCSS($_POST['extra_css']) === false)
		$errors['extra-css'] = ACP3_CMS::$lang->t('system', 'type_in_additional_stylesheets');
	if (!empty($_POST['extra_js']) && ACP3_Validate::extraJS($_POST['extra_js']) === false)
		$errors['extra-js'] = ACP3_CMS::$lang->t('system', 'type_in_additional_javascript_files');
	if ($_POST['mailer_type'] === 'smtp') {
		if (empty($_POST['mailer_smtp_host']))
			$errors['mailer-smtp-host'] = ACP3_CMS::$lang->t('system', 'type_in_mailer_smtp_host');
		if (ACP3_Validate::isNumber($_POST['mailer_smtp_port']) === false)
			$errors['mailer-smtp-port'] = ACP3_CMS::$lang->t('system', 'type_in_mailer_smtp_port');
		if ($_POST['mailer_smtp_auth'] == 1 && empty($_POST['mailer_smtp_user']))
			$errors['mailer-smtp-username'] = ACP3_CMS::$lang->t('system', 'type_in_mailer_smtp_username');
	}

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		// Config aktualisieren
		$config = array(
			'cache_images' => (int) $_POST['cache_images'],
			'cache_minify' => (int) $_POST['cache_minify'],
			'date_format_long' => str_encode($_POST['date_format_long']),
			'date_format_short' => str_encode($_POST['date_format_short']),
			'date_time_zone' => $_POST['date_time_zone'],
			'entries' => (int) $_POST['entries'],
			'extra_css' => $_POST['extra_css'],
			'extra_js' => $_POST['extra_js'],
			'flood' => (int) $_POST['flood'],
			'homepage' => $_POST['homepage'],
			'icons_path' => $_POST['icons_path'],
			'mailer_smtp_auth' => (int) $_POST['mailer_smtp_auth'],
			'mailer_smtp_host' => $_POST['mailer_smtp_host'],
			'mailer_smtp_password' => $_POST['mailer_smtp_password'],
			'mailer_smtp_port' => (int) $_POST['mailer_smtp_port'],
			'mailer_smtp_security' => $_POST['mailer_smtp_security'],
			'mailer_smtp_user' => $_POST['mailer_smtp_user'],
			'mailer_type' => $_POST['mailer_type'],
			'maintenance_message' => $_POST['maintenance_message'],
			'maintenance_mode' => (int) $_POST['maintenance_mode'],
			'seo_aliases' => (int) $_POST['seo_aliases'],
			'seo_meta_description' => str_encode($_POST['seo_meta_description']),
			'seo_meta_keywords' => str_encode($_POST['seo_meta_keywords']),
			'seo_mod_rewrite' => (int) $_POST['seo_mod_rewrite'],
			'seo_robots' => (int) $_POST['seo_robots'],
			'seo_title' => str_encode($_POST['seo_title']),
			'wysiwyg' => $_POST['wysiwyg']
		);

		$bool = ACP3_Config::setSettings('system', $config);

		// Gecachete Stylesheets und JavaScript Dateien löschen
		if (CONFIG_EXTRA_CSS !== $_POST['extra_css'] ||
			CONFIG_EXTRA_JS !== $_POST['extra_js']) {
			ACP3_Cache::purge('minify');
		}

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'), 'acp/system/configuration');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	getRedirectMessage();

	ACP3_CMS::$view->assign('entries', recordsPerPage(CONFIG_ENTRIES));

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
	$wysiwyg[$i]['lang'] = ACP3_CMS::$lang->t('system', 'textarea');
	ACP3_CMS::$view->assign('wysiwyg', $wysiwyg);

	// Zeitzonen
	ACP3_CMS::$view->assign('time_zones', ACP3_CMS::$date->getTimeZones(CONFIG_DATE_TIME_ZONE));

	// Wartungsmodus an/aus
	$lang_maintenance = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('maintenance', selectGenerator('maintenance_mode', array(1, 0), $lang_maintenance, CONFIG_MAINTENANCE_MODE, 'checked'));

	// Robots
	$lang_robots = array(
		ACP3_CMS::$lang->t('system', 'seo_robots_index_follow'),
		ACP3_CMS::$lang->t('system', 'seo_robots_index_nofollow'),
		ACP3_CMS::$lang->t('system', 'seo_robots_noindex_follow'),
		ACP3_CMS::$lang->t('system', 'seo_robots_noindex_nofollow')
	);
	ACP3_CMS::$view->assign('robots', selectGenerator('seo_robots', array(1, 2, 3, 4), $lang_robots, CONFIG_SEO_ROBOTS));

	// URI-Aliases aktivieren/deaktivieren
	$lang_aliases = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('aliases', selectGenerator('seo_aliases', array(1, 0), $lang_aliases, CONFIG_SEO_ALIASES, 'checked'));

	// Sef-URIs
	$lang_mod_rewrite = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('mod_rewrite', selectGenerator('seo_mod_rewrite', array(1, 0), $lang_mod_rewrite, CONFIG_SEO_MOD_REWRITE, 'checked'));

	// Caching von Bildern
	$lang_cache_images = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('cache_images', selectGenerator('cache_images', array(1, 0), $lang_cache_images, CONFIG_CACHE_IMAGES, 'checked'));

	// Mailertyp
	$lang_mailer_type = array(ACP3_CMS::$lang->t('system', 'mailer_type_php_mail'), ACP3_CMS::$lang->t('system', 'mailer_type_smtp'));
	ACP3_CMS::$view->assign('mailer_type', selectGenerator('mailer_type', array('mail', 'smtp'), $lang_mailer_type, CONFIG_MAILER_TYPE));

	// Mailer SMTP Authentifizierung
	$lang_mailer_smtp_auth = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('mailer_smtp_auth', selectGenerator('mailer_smtp_auth', array(1, 0), $lang_mailer_smtp_auth, CONFIG_MAILER_SMTP_AUTH, 'checked'));

	// Mailer SMTP Verschlüsselung
	$lang_mailer_smtp_security = array(
		ACP3_CMS::$lang->t('system', 'mailer_smtp_security_none'),
		ACP3_CMS::$lang->t('system', 'mailer_smtp_security_ssl'),
		ACP3_CMS::$lang->t('system', 'mailer_smtp_security_tls')
	);
	ACP3_CMS::$view->assign('mailer_smtp_security', selectGenerator('mailer_smtp_security', array('none', 'ssl', 'tls'), $lang_mailer_smtp_security, CONFIG_MAILER_SMTP_SECURITY));

	$settings = ACP3_Config::getSettings('system');

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3_CMS::$session->generateFormToken();
}