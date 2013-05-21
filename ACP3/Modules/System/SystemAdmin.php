<?php

namespace ACP3\Modules\System;

use ACP3\Core;

/**
 * Description of SystemAdmin
 *
 * @author Tino
 */
class SystemAdmin extends Core\ModuleController {

	public function actionConfiguration() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::isInternalURI($_POST['homepage']) === false)
				$errors['homepage'] = Core\Registry::get('Lang')->t('system', 'incorrect_homepage');
			if (Core\Validate::isNumber($_POST['entries']) === false)
				$errors['entries'] = Core\Registry::get('Lang')->t('system', 'select_records_per_page');
			if (Core\Validate::isNumber($_POST['flood']) === false)
				$errors['flood'] = Core\Registry::get('Lang')->t('system', 'type_in_flood_barrier');
			if ((bool) preg_match('/\/$/', $_POST['icons_path']) === false)
				$errors['icons-path'] = Core\Registry::get('Lang')->t('system', 'incorrect_path_to_icons');
			if (preg_match('=/=', $_POST['wysiwyg']) || is_file(CLASSES_DIR . 'WYSIWYG/' . $_POST['wysiwyg'] . '.php') === false)
				$errors['wysiwyg'] = Core\Registry::get('Lang')->t('system', 'select_editor');
			if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
				$errors[] = Core\Registry::get('Lang')->t('system', 'type_in_date_format');
			if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
				$errors['date-time-zone'] = Core\Registry::get('Lang')->t('system', 'select_time_zone');
			if (Core\Validate::isNumber($_POST['maintenance_mode']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_online_maintenance');
			if (strlen($_POST['maintenance_message']) < 3)
				$errors['maintenance-message'] = Core\Registry::get('Lang')->t('system', 'maintenance_message_to_short');
			if (empty($_POST['seo_title']))
				$errors['seo-title'] = Core\Registry::get('Lang')->t('system', 'title_to_short');
			if (Core\Validate::isNumber($_POST['seo_robots']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_seo_robots');
			if (Core\Validate::isNumber($_POST['seo_aliases']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_seo_aliases');
			if (Core\Validate::isNumber($_POST['seo_mod_rewrite']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_mod_rewrite');
			if (Core\Validate::isNumber($_POST['cache_images']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_cache_images');
			if (Core\Validate::isNumber($_POST['cache_minify']) === false)
				$errors['cache-minify'] = Core\Registry::get('Lang')->t('system', 'type_in_minify_cache_lifetime');
			if (!empty($_POST['extra_css']) && Core\Validate::extraCSS($_POST['extra_css']) === false)
				$errors['extra-css'] = Core\Registry::get('Lang')->t('system', 'type_in_additional_stylesheets');
			if (!empty($_POST['extra_js']) && Core\Validate::extraJS($_POST['extra_js']) === false)
				$errors['extra-js'] = Core\Registry::get('Lang')->t('system', 'type_in_additional_javascript_files');
			if ($_POST['mailer_type'] === 'smtp') {
				if (empty($_POST['mailer_smtp_host']))
					$errors['mailer-smtp-host'] = Core\Registry::get('Lang')->t('system', 'type_in_mailer_smtp_host');
				if (Core\Validate::isNumber($_POST['mailer_smtp_port']) === false)
					$errors['mailer-smtp-port'] = Core\Registry::get('Lang')->t('system', 'type_in_mailer_smtp_port');
				if ($_POST['mailer_smtp_auth'] == 1 && empty($_POST['mailer_smtp_user']))
					$errors['mailer-smtp-username'] = Core\Registry::get('Lang')->t('system', 'type_in_mailer_smtp_username');
			}

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				// Config aktualisieren
				$config = array(
					'cache_images' => (int) $_POST['cache_images'],
					'cache_minify' => (int) $_POST['cache_minify'],
					'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
					'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
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
					'seo_meta_description' => Core\Functions::strEncode($_POST['seo_meta_description']),
					'seo_meta_keywords' => Core\Functions::strEncode($_POST['seo_meta_keywords']),
					'seo_mod_rewrite' => (int) $_POST['seo_mod_rewrite'],
					'seo_robots' => (int) $_POST['seo_robots'],
					'seo_title' => Core\Functions::strEncode($_POST['seo_title']),
					'wysiwyg' => $_POST['wysiwyg']
				);

				$bool = Core\Config::setSettings('system', $config);

				// Gecachete Stylesheets und JavaScript Dateien löschen
				if (CONFIG_EXTRA_CSS !== $_POST['extra_css'] ||
						CONFIG_EXTRA_JS !== $_POST['extra_js']) {
					Core\Cache::purge('minify');
				}

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'), 'acp/system/configuration');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Functions::getRedirectMessage();

			Core\Registry::get('View')->assign('entries', Core\Functions::recordsPerPage(CONFIG_ENTRIES));

			// WYSIWYG-Editoren
			$editors = scandir(CLASSES_DIR . 'WYSIWYG');
			$c_editors = count($editors);
			$wysiwyg = array();

			for ($i = 0; $i < $c_editors; ++$i) {
				$editors[$i] = substr($editors[$i], 0, strrpos($editors[$i], '.php'));
				if (!empty($editors[$i]) && !in_array($editors[$i], array('.', '..', 'AbstractWYSIWYG'))) {
					$wysiwyg[$i]['value'] = $editors[$i];
					$wysiwyg[$i]['selected'] = Core\Functions::selectEntry('wysiwyg', $editors[$i], CONFIG_WYSIWYG);
					$wysiwyg[$i]['lang'] = $editors[$i];
				}
			}
			Core\Registry::get('View')->assign('wysiwyg', $wysiwyg);

			// Zeitzonen
			Core\Registry::get('View')->assign('time_zones', Core\Date::getTimeZones(CONFIG_DATE_TIME_ZONE));

			// Wartungsmodus an/aus
			$lang_maintenance = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('maintenance', Core\Functions::selectGenerator('maintenance_mode', array(1, 0), $lang_maintenance, CONFIG_MAINTENANCE_MODE, 'checked'));

			// Robots
			$lang_robots = array(
				Core\Registry::get('Lang')->t('system', 'seo_robots_index_follow'),
				Core\Registry::get('Lang')->t('system', 'seo_robots_index_nofollow'),
				Core\Registry::get('Lang')->t('system', 'seo_robots_noindex_follow'),
				Core\Registry::get('Lang')->t('system', 'seo_robots_noindex_nofollow')
			);
			Core\Registry::get('View')->assign('robots', Core\Functions::selectGenerator('seo_robots', array(1, 2, 3, 4), $lang_robots, CONFIG_SEO_ROBOTS));

			// URI-Aliases aktivieren/deaktivieren
			$lang_aliases = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('aliases', Core\Functions::selectGenerator('seo_aliases', array(1, 0), $lang_aliases, CONFIG_SEO_ALIASES, 'checked'));

			// Sef-URIs
			$lang_mod_rewrite = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('mod_rewrite', Core\Functions::selectGenerator('seo_mod_rewrite', array(1, 0), $lang_mod_rewrite, CONFIG_SEO_MOD_REWRITE, 'checked'));

			// Caching von Bildern
			$lang_cache_images = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('cache_images', Core\Functions::selectGenerator('cache_images', array(1, 0), $lang_cache_images, CONFIG_CACHE_IMAGES, 'checked'));

			// Mailertyp
			$lang_mailer_type = array(Core\Registry::get('Lang')->t('system', 'mailer_type_php_mail'), Core\Registry::get('Lang')->t('system', 'mailer_type_smtp'));
			Core\Registry::get('View')->assign('mailer_type', Core\Functions::selectGenerator('mailer_type', array('mail', 'smtp'), $lang_mailer_type, CONFIG_MAILER_TYPE));

			// Mailer SMTP Authentifizierung
			$lang_mailer_smtp_auth = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('mailer_smtp_auth', Core\Functions::selectGenerator('mailer_smtp_auth', array(1, 0), $lang_mailer_smtp_auth, CONFIG_MAILER_SMTP_AUTH, 'checked'));

			// Mailer SMTP Verschlüsselung
			$lang_mailer_smtp_security = array(
				Core\Registry::get('Lang')->t('system', 'mailer_smtp_security_none'),
				Core\Registry::get('Lang')->t('system', 'mailer_smtp_security_ssl'),
				Core\Registry::get('Lang')->t('system', 'mailer_smtp_security_tls')
			);
			Core\Registry::get('View')->assign('mailer_smtp_security', Core\Functions::selectGenerator('mailer_smtp_security', array('none', 'ssl', 'tls'), $lang_mailer_smtp_security, CONFIG_MAILER_SMTP_SECURITY));

			$settings = Core\Config::getSettings('system');

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionDesigns() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('system', 'acp_extensions'), Core\Registry::get('URI')->route('acp/system/extensions'))
				->append(Core\Registry::get('Lang')->t('system', 'acp_designs'));

		if (isset(Core\Registry::get('URI')->dir)) {
			$bool = false;

			if ((bool) preg_match('=/=', Core\Registry::get('URI')->dir) === false &&
					is_file(ACP3_ROOT_DIR . 'designs/' . Core\Registry::get('URI')->dir . '/info.xml') === true) {
				$bool = Core\Config::setSettings('system', array('design' => Core\Registry::get('URI')->dir));

				// Template Cache leeren
				Core\Cache::purge('tpl_compiled');
				Core\Cache::purge('tpl_cached');
			}
			$text = Core\Registry::get('Lang')->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

			Core\Functions::setRedirectMessage($bool, $text, 'acp/system/designs');
		} else {
			Core\Functions::getRedirectMessage();

			$designs = array();
			$path = ACP3_ROOT_DIR . 'designs/';
			$directories = scandir($path);
			$count_dir = count($directories);
			for ($i = 0; $i < $count_dir; ++$i) {
				$design_info = Core\XML::parseXmlFile($path . $directories[$i] . '/info.xml', '/design');
				if (!empty($design_info)) {
					$designs[$i] = $design_info;
					$designs[$i]['selected'] = CONFIG_DESIGN === $directories[$i] ? 1 : 0;
					$designs[$i]['dir'] = $directories[$i];
				}
			}
			Core\Registry::get('View')->assign('designs', $designs);
		}
	}

	public function actionExtensions() {
		Core\Registry::get('View')->setContentTemplate('system/acp_extensions.tpl');
	}

	public function actionLanguages() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('system', 'acp_extensions'), Core\Registry::get('URI')->route('acp/system/extensions'))
				->append(Core\Registry::get('Lang')->t('system', 'acp_languages'));

		if (isset(Core\Registry::get('URI')->dir)) {
			$bool = false;

			if (Core\Registry::get('Lang')->languagePackExists(Core\Registry::get('URI')->dir) === true) {
				$bool = Core\Config::setSettings('system', array('lang' => Core\Registry::get('URI')->dir));
				Core\Registry::get('Lang')->setLanguage(Core\Registry::get('URI')->dir);
			}
			$text = Core\Registry::get('Lang')->t('system', $bool === true ? 'languages_edit_success' : 'languages_edit_error');

			Core\Functions::setRedirectMessage($bool, $text, 'acp/system/languages');
		} else {
			Core\Functions::getRedirectMessage();

			$languages = array();
			$directories = scandir(ACP3_ROOT_DIR . 'languages');
			$count_dir = count($directories);
			for ($i = 0; $i < $count_dir; ++$i) {
				$lang_info = Core\XML::parseXmlFile(ACP3_ROOT_DIR . 'languages/' . $directories[$i] . '/info.xml', '/language');
				if (!empty($lang_info)) {
					$languages[$i] = $lang_info;
					$languages[$i]['selected'] = CONFIG_LANG == $directories[$i] ? 1 : 0;
					$languages[$i]['dir'] = $directories[$i];
				}
			}
			Core\Registry::get('View')->assign('languages', $languages);
		}
	}

	public function actionList() {
		Core\Registry::get('View')->setContentTemplate('system/acp_list.tpl');
	}

	public function actionMaintenance() {
		Core\Registry::get('View')->setContentTemplate('system/acp_maintenance.tpl');
	}

	public function actionModules() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('system', 'acp_extensions'), Core\Registry::get('URI')->route('acp/system/extensions'))
				->append(Core\Registry::get('Lang')->t('system', 'acp_modules'));

		switch (Core\Registry::get('URI')->action) {
			case 'activate':
				$this->enableModule();
				break;
			case 'deactivate':
				$this->disableModule();
				break;
			case 'install':
				$this->installModule();
				break;
			case 'uninstall':
				$this->uninstallModule();
				break;
			default:
				Core\Functions::getRedirectMessage();

				// Languagecache neu erstellen, für den Fall, dass neue Module hinzugefügt wurden
				Core\Registry::get('Lang')->setLanguageCache();

				$modules = Core\Modules::getAllModules();
				$installed_modules = $new_modules = array();

				foreach ($modules as $key => $values) {
					$values['dir'] = strtolower($values['dir']);
					if (Core\Modules::isInstalled($values['dir']) === true) {
						$installed_modules[$key] = $values;
					} else {
						$new_modules[$key] = $values;
					}
				}

				Core\Registry::get('View')->assign('installed_modules', $installed_modules);
				Core\Registry::get('View')->assign('new_modules', $new_modules);
		}
	}

	private function enableModule() {
		$bool = false;
		$info = Core\Modules::getModuleInfo(Core\Registry::get('URI')->dir);
		if (empty($info)) {
			$text = Core\Registry::get('Lang')->t('system', 'module_not_found');
		} elseif ($info['protected'] === true) {
			$text = Core\Registry::get('Lang')->t('system', 'mod_deactivate_forbidden');
		} else {
			$bool = Core\Registry::get('Db')->update(DB_PRE . 'modules', array('active' => 1), array('name' => Core\Registry::get('URI')->dir));
			Core\Modules::setModulesCache();
			Core\ACL::setResourcesCache();

			$text = Core\Registry::get('Lang')->t('system', 'mod_activate_' . ($bool !== false ? 'success' : 'error'));
		}
		Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
	}

	private function disableModule() {
		$bool = false;
		$info = Core\Modules::getModuleInfo(Core\Registry::get('URI')->dir);
		if (empty($info)) {
			$text = Core\Registry::get('Lang')->t('system', 'module_not_found');
		} elseif ($info['protected'] === true) {
			$text = Core\Registry::get('Lang')->t('system', 'mod_deactivate_forbidden');
		} else {
			// Modulabhängigkeiten prüfen
			$deps = SystemFunctions::checkUninstallDependencies(Core\Registry::get('URI')->dir);

			if (empty($deps)) {
				$bool = Core\Registry::get('Db')->update(DB_PRE . 'modules', array('active' => 0), array('name' => Core\Registry::get('URI')->dir));
				Core\Modules::setModulesCache();
				Core\ACL::setResourcesCache();

				$text = Core\Registry::get('Lang')->t('system', 'mod_deactivate_' . ($bool !== false ? 'success' : 'error'));
			} else {
				$text = sprintf(Core\Registry::get('Lang')->t('system', 'module_disable_not_possible'), implode(', ', $deps));
			}
		}
		Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
	}

	private function installModule() {
		$bool = false;
		// Nur noch nicht installierte Module berücksichtigen
		if (Core\Modules::isInstalled(Core\Registry::get('URI')->dir) === false) {
			$mod_name = ucfirst(Core\Registry::get('URI')->dir);
			$path = MODULES_DIR . $mod_name . '/' . $mod_name . 'Installer.php';
			if (is_file($path) === true) {
				// Modulabhängigkeiten prüfen
				$deps = SystemFunctions::checkInstallDependencies(Core\Registry::get('URI')->dir);

				// Modul installieren
				if (empty($deps)) {
					$className = Core\ModuleInstaller::buildClassName(Core\Registry::get('URI')->dir);
					$install = new $className();
					$bool = $install->install();
					Core\Modules::setModulesCache();
					$text = Core\Registry::get('Lang')->t('system', 'mod_installation_' . ($bool !== false ? 'success' : 'error'));
				} else {
					$text = sprintf(Core\Registry::get('Lang')->t('system', 'enable_following_modules_first'), implode(', ', $deps));
				}
			} else {
				$text = Core\Registry::get('Lang')->t('system', 'module_installer_not_found');
			}
		} else {
			$text = Core\Registry::get('Lang')->t('system', 'module_already_installed');
		}
		Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
	}

	private function uninstallModule() {
		$bool = false;
		$mod_info = Core\Modules::getModuleInfo(Core\Registry::get('URI')->dir);
		// Nur installierte und Nicht-Core-Module berücksichtigen
		if ($mod_info['protected'] === false && Core\Modules::isInstalled(Core\Registry::get('URI')->dir) === true) {
			$mod_name = ucfirst(Core\Registry::get('URI')->dir);
			$path = MODULES_DIR . $mod_name . '/' . $mod_name . 'Installer.php';
			if (is_file($path) === true) {
				// Modulabhängigkeiten prüfen
				$deps = SystemFunctions::checkUninstallDependencies(Core\Registry::get('URI')->dir);

				// Modul deinstallieren
				if (empty($deps)) {
					$className = Core\ModuleInstaller::buildClassName(Core\Registry::get('URI')->dir);
					$install = new $className();
					$bool = $install->uninstall();
					Core\Modules::setModulesCache();
					$text = Core\Registry::get('Lang')->t('system', 'mod_uninstallation_' . ($bool !== false ? 'success' : 'error'));
				} else {
					$text = sprintf(Core\Registry::get('Lang')->t('system', 'uninstall_following_modules_first'), implode(', ', $deps));
				}
			} else {
				$text = Core\Registry::get('Lang')->t('system', 'module_installer_not_found');
			}
		} else {
			$text = Core\Registry::get('Lang')->t('system', 'protected_module_description');
		}
		Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
	}

	public function actionSqlExport() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('system', 'acp_maintenance'), Core\Registry::get('URI')->route('acp/system/maintenance'))
				->append(Core\Registry::get('Lang')->t('system', 'acp_sql_export'));

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['tables']) || is_array($_POST['tables']) === false)
				$errors['tables'] = Core\Registry::get('Lang')->t('system', 'select_sql_tables');
			if ($_POST['output'] !== 'file' && $_POST['output'] !== 'text')
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_output');
			if (in_array($_POST['export_type'], array('complete', 'structure', 'data')) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_export_type');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				Core\Registry::get('Session')->unsetFormToken();

				$structure = '';
				$data = '';
				foreach ($_POST['tables'] as $table) {
					// Struktur ausgeben
					if ($_POST['export_type'] === 'complete' || $_POST['export_type'] === 'structure') {
						$result = Core\Registry::get('Db')->fetchAssoc('SHOW CREATE TABLE ' . $table);
						if (!empty($result)) {
							$structure.= isset($_POST['drop']) && $_POST['drop'] == 1 ? 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n" : '';
							$structure.= $result['Create Table'] . ';' . "\n\n";
						}
					}

					// Datensätze ausgeben
					if ($_POST['export_type'] === 'complete' || $_POST['export_type'] === 'data') {
						$resultsets = Core\Registry::get('Db')->fetchAll('SELECT * FROM ' . DB_PRE . substr($table, strlen(CONFIG_DB_PRE)));
						if (count($resultsets) > 0) {
							$fields = '';
							// Felder der jeweiligen Tabelle auslesen
							foreach (array_keys($resultsets[0]) as $field) {
								$fields.= '`' . $field . '`, ';
							}

							// Datensätze auslesen
							foreach ($resultsets as $row) {
								$values = '';
								foreach ($row as $value) {
									$values.= '\'' . $value . '\', ';
								}
								$data.= 'INSERT INTO `' . $table . '` (' . substr($fields, 0, -2) . ') VALUES (' . substr($values, 0, -2) . ');' . "\n";
							}
						}
					}
				}
				$export = $structure . $data;

				// Als Datei ausgeben
				if ($_POST['output'] === 'file') {
					header('Content-Type: text/sql');
					header('Content-Disposition: attachment; filename=' . CONFIG_DB_NAME . '_export.sql');
					exit($export);
					// Im Browser ausgeben
				} else {
					Core\Registry::get('View')->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
				}
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$db_tables = Core\Registry::get('Db')->fetchAll('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_TYPE = ? AND TABLE_SCHEMA = ?', array('BASE TABLE', CONFIG_DB_NAME));
			$tables = array();
			foreach ($db_tables as $row) {
				$table = $row['TABLE_NAME'];
				if (strpos($table, CONFIG_DB_PRE) === 0) {
					$tables[$table]['name'] = $table;
					$tables[$table]['selected'] = Core\Functions::selectEntry('tables', $table);
				}
			}
			ksort($tables);
			Core\Registry::get('View')->assign('tables', $tables);

			// Ausgabe
			$lang_output = array(Core\Registry::get('Lang')->t('system', 'output_as_file'), Core\Registry::get('Lang')->t('system', 'output_as_text'));
			Core\Registry::get('View')->assign('output', Core\Functions::selectGenerator('output', array('file', 'text'), $lang_output, 'file', 'checked'));

			// Exportart
			$lang_export_type = array(
				Core\Registry::get('Lang')->t('system', 'complete_export'),
				Core\Registry::get('Lang')->t('system', 'export_structure'),
				Core\Registry::get('Lang')->t('system', 'export_data')
			);
			Core\Registry::get('View')->assign('export_type', Core\Functions::selectGenerator('export_type', array('complete', 'structure', 'data'), $lang_export_type, 'complete', 'checked'));

			$drop = array();
			$drop['checked'] = Core\Functions::selectEntry('drop', '1', '', 'checked');
			$drop['lang'] = Core\Registry::get('Lang')->t('system', 'drop_tables');
			Core\Registry::get('View')->assign('drop', $drop);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionSqlImport() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('system', 'acp_maintenance'), Core\Registry::get('URI')->route('acp/system/maintenance'))
				->append(Core\Registry::get('Lang')->t('system', 'acp_sql_import'));

		if (isset($_POST['submit']) === true) {
			if (isset($_FILES['file'])) {
				$file['tmp_name'] = $_FILES['file']['tmp_name'];
				$file['name'] = $_FILES['file']['name'];
				$file['size'] = $_FILES['file']['size'];
			}

			if (empty($_POST['text']) && empty($file['size']))
				$errors['text'] = Core\Registry::get('Lang')->t('system', 'type_in_text_or_select_sql_file');
			if (!empty($file['size']) &&
					(!Core\Validate::mimeType($file['tmp_name'], 'text/plain') ||
					$_FILES['file']['error'] !== UPLOAD_ERR_OK))
				$errors['file'] = Core\Registry::get('Lang')->t('system', 'select_sql_file');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				Core\Registry::get('Session')->unsetFormToken();

				$data = isset($file) ? file_get_contents($file['tmp_name']) : $_POST['text'];
				$data_ary = explode(";\n", str_replace(array("\r\n", "\r", "\n"), "\n", $data));
				$sql_queries = array();

				$i = 0;
				foreach ($data_ary as $row) {
					if (!empty($row)) {
						$bool = Core\Registry::get('Db')->query($row);
						$sql_queries[$i]['query'] = str_replace("\n", '<br />', $row);
						$sql_queries[$i]['color'] = $bool !== null ? '090' : 'f00';
						++$i;

						if (!$bool) {
							break;
						}
					}
				}

				Core\Registry::get('View')->assign('sql_queries', $sql_queries);

				Core\Cache::purge();
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('text' => ''));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionUpdateCheck() {
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('system', 'acp_maintenance'), Core\Registry::get('URI')->route('acp/system/maintenance'))
				->append(Core\Registry::get('Lang')->t('system', 'acp_update_check'));

		$file = @file_get_contents('http://www.acp3-cms.net/update.txt');
		if ($file !== false) {
			$data = explode('||', $file);
			if (count($data) === 2) {
				$update = array(
					'installed_version' => CONFIG_VERSION,
					'current_version' => $data[0],
				);

				if (version_compare($update['installed_version'], $update['current_version'], '>=')) {
					$update['text'] = Core\Registry::get('Lang')->t('system', 'acp3_up_to_date');
					$update['class'] = 'success';
				} else {
					$update['text'] = sprintf(Core\Registry::get('Lang')->t('system', 'acp3_not_up_to_date'), '<a href="' . $data[1] . '" onclick="window.open(this.href); return false">', '</a>');
					$update['class'] = 'error';
				}

				Core\Registry::get('View')->assign('update', $update);
			}
		}
	}

}