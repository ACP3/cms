<?php

namespace ACP3\Modules\System;

use ACP3\Core;

/**
 * Description of SystemAdmin
 *
 * @author Tino
 */
class SystemAdmin extends Core\ModuleController {

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionConfiguration()
	{
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::isInternalURI($_POST['homepage']) === false)
				$errors['homepage'] = $this->injector['Lang']->t('system', 'incorrect_homepage');
			if (Core\Validate::isNumber($_POST['entries']) === false)
				$errors['entries'] = $this->injector['Lang']->t('system', 'select_records_per_page');
			if (Core\Validate::isNumber($_POST['flood']) === false)
				$errors['flood'] = $this->injector['Lang']->t('system', 'type_in_flood_barrier');
			if ((bool) preg_match('/\/$/', $_POST['icons_path']) === false)
				$errors['icons-path'] = $this->injector['Lang']->t('system', 'incorrect_path_to_icons');
			if ($_POST['wysiwyg'] != 'textarea' && (preg_match('=/=', $_POST['wysiwyg']) || is_file(ACP3_DIR . 'wysiwyg/' . $_POST['wysiwyg'] . '/info.xml') === false))
				$errors['wysiwyg'] = $this->injector['Lang']->t('system', 'select_editor');
			if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
				$errors[] = $this->injector['Lang']->t('system', 'type_in_date_format');
			if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
				$errors['date-time-zone'] = $this->injector['Lang']->t('system', 'select_time_zone');
			if (Core\Validate::isNumber($_POST['maintenance_mode']) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_online_maintenance');
			if (strlen($_POST['maintenance_message']) < 3)
				$errors['maintenance-message'] = $this->injector['Lang']->t('system', 'maintenance_message_to_short');
			if (empty($_POST['seo_title']))
				$errors['seo-title'] = $this->injector['Lang']->t('system', 'title_to_short');
			if (Core\Validate::isNumber($_POST['seo_robots']) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_seo_robots');
			if (Core\Validate::isNumber($_POST['seo_aliases']) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_seo_aliases');
			if (Core\Validate::isNumber($_POST['seo_mod_rewrite']) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_mod_rewrite');
			if (Core\Validate::isNumber($_POST['cache_images']) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_cache_images');
			if (Core\Validate::isNumber($_POST['cache_minify']) === false)
				$errors['cache-minify'] = $this->injector['Lang']->t('system', 'type_in_minify_cache_lifetime');
			if (!empty($_POST['extra_css']) && Core\Validate::extraCSS($_POST['extra_css']) === false)
				$errors['extra-css'] = $this->injector['Lang']->t('system', 'type_in_additional_stylesheets');
			if (!empty($_POST['extra_js']) && Core\Validate::extraJS($_POST['extra_js']) === false)
				$errors['extra-js'] = $this->injector['Lang']->t('system', 'type_in_additional_javascript_files');
			if ($_POST['mailer_type'] === 'smtp') {
				if (empty($_POST['mailer_smtp_host']))
					$errors['mailer-smtp-host'] = $this->injector['Lang']->t('system', 'type_in_mailer_smtp_host');
				if (Core\Validate::isNumber($_POST['mailer_smtp_port']) === false)
					$errors['mailer-smtp-port'] = $this->injector['Lang']->t('system', 'type_in_mailer_smtp_port');
				if ($_POST['mailer_smtp_auth'] == 1 && empty($_POST['mailer_smtp_user']))
					$errors['mailer-smtp-username'] = $this->injector['Lang']->t('system', 'type_in_mailer_smtp_username');
			}

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				// Config aktualisieren
				$config = array(
					'cache_images' => (int) $_POST['cache_images'],
					'cache_minify' => (int) $_POST['cache_minify'],
					'date_format_long' => Core\Functions::str_encode($_POST['date_format_long']),
					'date_format_short' => Core\Functions::str_encode($_POST['date_format_short']),
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
					'seo_meta_description' => Core\Functions::str_encode($_POST['seo_meta_description']),
					'seo_meta_keywords' => Core\Functions::str_encode($_POST['seo_meta_keywords']),
					'seo_mod_rewrite' => (int) $_POST['seo_mod_rewrite'],
					'seo_robots' => (int) $_POST['seo_robots'],
					'seo_title' => Core\Functions::str_encode($_POST['seo_title']),
					'wysiwyg' => $_POST['wysiwyg']
				);

				$bool = Core\Config::setSettings('system', $config);

				// Gecachete Stylesheets und JavaScript Dateien löschen
				if (CONFIG_EXTRA_CSS !== $_POST['extra_css'] ||
						CONFIG_EXTRA_JS !== $_POST['extra_js']) {
					Core\Cache::purge('minify');
				}

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'), 'acp/system/configuration');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Functions::getRedirectMessage();

			$this->injector['View']->assign('entries', Core\Functions::recordsPerPage(CONFIG_ENTRIES));

			// WYSIWYG-Editoren
			$editors = scandir(ACP3_DIR . 'wysiwyg');
			$c_editors = count($editors);
			$wysiwyg = array();

			for ($i = 0; $i < $c_editors; ++$i) {
				$info = Core\XML::parseXmlFile(ACP3_DIR . 'wysiwyg/' . $editors[$i] . '/info.xml', '/editor');
				if (!empty($info)) {
					$wysiwyg[$i]['value'] = $editors[$i];
					$wysiwyg[$i]['selected'] = Core\Functions::selectEntry('wysiwyg', $editors[$i], CONFIG_WYSIWYG);
					$wysiwyg[$i]['lang'] = $info['name'] . ' ' . $info['version'];
				}
			}
			// Normale <textarea>
			$wysiwyg[$i]['value'] = 'textarea';
			$wysiwyg[$i]['selected'] = Core\Functions::selectEntry('wysiwyg', 'textarea', CONFIG_WYSIWYG);
			$wysiwyg[$i]['lang'] = $this->injector['Lang']->t('system', 'textarea');
			$this->injector['View']->assign('wysiwyg', $wysiwyg);

			// Zeitzonen
			$this->injector['View']->assign('time_zones', $this->injector['Date']->getTimeZones(CONFIG_DATE_TIME_ZONE));

			// Wartungsmodus an/aus
			$lang_maintenance = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('maintenance', Core\Functions::selectGenerator('maintenance_mode', array(1, 0), $lang_maintenance, CONFIG_MAINTENANCE_MODE, 'checked'));

			// Robots
			$lang_robots = array(
				$this->injector['Lang']->t('system', 'seo_robots_index_follow'),
				$this->injector['Lang']->t('system', 'seo_robots_index_nofollow'),
				$this->injector['Lang']->t('system', 'seo_robots_noindex_follow'),
				$this->injector['Lang']->t('system', 'seo_robots_noindex_nofollow')
			);
			$this->injector['View']->assign('robots', Core\Functions::selectGenerator('seo_robots', array(1, 2, 3, 4), $lang_robots, CONFIG_SEO_ROBOTS));

			// URI-Aliases aktivieren/deaktivieren
			$lang_aliases = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('aliases', Core\Functions::selectGenerator('seo_aliases', array(1, 0), $lang_aliases, CONFIG_SEO_ALIASES, 'checked'));

			// Sef-URIs
			$lang_mod_rewrite = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('mod_rewrite', Core\Functions::selectGenerator('seo_mod_rewrite', array(1, 0), $lang_mod_rewrite, CONFIG_SEO_MOD_REWRITE, 'checked'));

			// Caching von Bildern
			$lang_cache_images = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('cache_images', Core\Functions::selectGenerator('cache_images', array(1, 0), $lang_cache_images, CONFIG_CACHE_IMAGES, 'checked'));

			// Mailertyp
			$lang_mailer_type = array($this->injector['Lang']->t('system', 'mailer_type_php_mail'), $this->injector['Lang']->t('system', 'mailer_type_smtp'));
			$this->injector['View']->assign('mailer_type', Core\Functions::selectGenerator('mailer_type', array('mail', 'smtp'), $lang_mailer_type, CONFIG_MAILER_TYPE));

			// Mailer SMTP Authentifizierung
			$lang_mailer_smtp_auth = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('mailer_smtp_auth', Core\Functions::selectGenerator('mailer_smtp_auth', array(1, 0), $lang_mailer_smtp_auth, CONFIG_MAILER_SMTP_AUTH, 'checked'));

			// Mailer SMTP Verschlüsselung
			$lang_mailer_smtp_security = array(
				$this->injector['Lang']->t('system', 'mailer_smtp_security_none'),
				$this->injector['Lang']->t('system', 'mailer_smtp_security_ssl'),
				$this->injector['Lang']->t('system', 'mailer_smtp_security_tls')
			);
			$this->injector['View']->assign('mailer_smtp_security', Core\Functions::selectGenerator('mailer_smtp_security', array('none', 'ssl', 'tls'), $lang_mailer_smtp_security, CONFIG_MAILER_SMTP_SECURITY));

			$settings = Core\Config::getSettings('system');

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionDesigns()
	{
		$this->injector['Breadcrumb']
				->append($this->injector['Lang']->t('system', 'acp_extensions'), $this->injector['URI']->route('acp/system/extensions'))
				->append($this->injector['Lang']->t('system', 'acp_designs'));

		if (isset($this->injector['URI']->dir)) {
			$bool = false;

			if ((bool) preg_match('=/=', $this->injector['URI']->dir) === false &&
					is_file(ACP3_ROOT_DIR . 'designs/' . $this->injector['URI']->dir . '/info.xml') === true) {
				$bool = Core\Config::setSettings('system', array('design' => $this->injector['URI']->dir));

				// Template Cache leeren
				Core\Cache::purge('tpl_compiled');
				Core\Cache::purge('tpl_cached');
			}
			$text = $this->injector['Lang']->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

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
			$this->injector['View']->assign('designs', $designs);
		}
	}

	public function actionExtensions()
	{
		$this->injector['View']->setContentTemplate('system/acp_extensions.tpl');
	}

	public function actionLanguages()
	{
		$this->injector['Breadcrumb']
				->append($this->injector['Lang']->t('system', 'acp_extensions'), $this->injector['URI']->route('acp/system/extensions'))
				->append($this->injector['Lang']->t('system', 'acp_languages'));

		if (isset($this->injector['URI']->dir)) {
			$bool = false;

			if ($this->injector['Lang']->languagePackExists($this->injector['URI']->dir) === true) {
				$bool = Core\Config::setSettings('system', array('lang' => $this->injector['URI']->dir));
				$this->injector['Lang']->setLanguage($this->injector['URI']->dir);
			}
			$text = $this->injector['Lang']->t('system', $bool === true ? 'languages_edit_success' : 'languages_edit_error');

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
			$this->injector['View']->assign('languages', $languages);
		}
	}

	public function actionList()
	{
		$this->injector['View']->setContentTemplate('system/acp_list.tpl');
	}

	public function actionMaintenance()
	{
		$this->injector['View']->setContentTemplate('system/acp_maintenance.tpl');
	}

	public function actionModules()
	{
		$this->injector['Breadcrumb']
				->append($this->injector['Lang']->t('system', 'acp_extensions'), $this->injector['URI']->route('acp/system/extensions'))
				->append($this->injector['Lang']->t('system', 'acp_modules'));

		switch ($this->injector['URI']->action) {
			case 'activate':
				$bool = false;
				$info = Core\Modules::getModuleInfo($this->injector['URI']->dir);
				if (empty($info)) {
					$text = $this->injector['Lang']->t('system', 'module_not_found');
				} elseif ($info['protected'] === true) {
					$text = $this->injector['Lang']->t('system', 'mod_deactivate_forbidden');
				} else {
					$bool = $this->injector['Db']->update(DB_PRE . 'modules', array('active' => 1), array('name' => $this->injector['URI']->dir));
					Core\Modules::setModulesCache();
					Core\ACL::setResourcesCache();

					$text = $this->injector['Lang']->t('system', 'mod_activate_' . ($bool !== false ? 'success' : 'error'));
				}
				Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
			case 'deactivate':
				$bool = false;
				$info = Core\Modules::getModuleInfo($this->injector['URI']->dir);
				if (empty($info)) {
					$text = $this->injector['Lang']->t('system', 'module_not_found');
				} elseif ($info['protected'] === true) {
					$text = $this->injector['Lang']->t('system', 'mod_deactivate_forbidden');
				} else {
					// Modulabhängigkeiten prüfen
					$deps = SystemFunctions::checkUninstallDependencies($this->injector['URI']->dir);

					if (empty($deps)) {
						$bool = $this->injector['Db']->update(DB_PRE . 'modules', array('active' => 0), array('name' => $this->injector['URI']->dir));
						Core\Modules::setModulesCache();
						Core\ACL::setResourcesCache();

						$text = $this->injector['Lang']->t('system', 'mod_deactivate_' . ($bool !== false ? 'success' : 'error'));
					} else {
						$text = sprintf($this->injector['Lang']->t('system', 'module_disable_not_possible'), implode(', ', $deps));
					}
				}
				Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
				break;
			case 'install':
				$bool = false;
				// Nur noch nicht installierte Module berücksichtigen
				if ($this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'modules WHERE name = ?', array($this->injector['URI']->dir)) == 0) {
					$path = MODULES_DIR . $this->injector['URI']->dir . '/install.class.php';
					if (is_file($path) === true) {
						// Modulabhängigkeiten prüfen
						$deps = SystemFunctions::checkInstallDependencies($this->injector['URI']->dir);

						// Modul installieren
						if (empty($deps)) {
							require_once $path;

							$className = Core\ModuleInstaller::buildClassName($this->injector['URI']->dir);
							$install = new $className();
							$bool = $install->install();
							Core\Modules::setModulesCache();
							$text = $this->injector['Lang']->t('system', 'mod_installation_' . ($bool !== false ? 'success' : 'error'));
						} else {
							$text = sprintf($this->injector['Lang']->t('system', 'enable_following_modules_first'), implode(', ', $deps));
						}
					} else {
						$text = $this->injector['Lang']->t('system', 'module_installer_dot_found');
					}
				} else {
					$text = $this->injector['Lang']->t('system', 'module_already_installed');
				}
				Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
				break;
			case 'uninstall':
				$bool = false;
				$mod_info = Core\Modules::getModuleInfo($this->injector['URI']->dir);
				// Nur installierte und Nicht-Core-Module berücksichtigen
				if ($mod_info['protected'] === false &&
						$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'modules WHERE name = ?', array($this->injector['URI']->dir)) == 1) {
					$path = MODULES_DIR . $this->injector['URI']->dir . '/install.class.php';
					if (is_file($path) === true) {
						// Modulabhängigkeiten prüfen
						$deps = SystemFunctions::checkUninstallDependencies($this->injector['URI']->dir);

						// Modul deinstallieren
						if (empty($deps)) {
							require_once $path;

							$className = Core\ModuleInstaller::buildClassName($this->injector['URI']->dir);
							$install = new $className();
							$bool = $install->uninstall();
							Core\Modules::setModulesCache();
							$text = $this->injector['Lang']->t('system', 'mod_uninstallation_' . ($bool !== false ? 'success' : 'error'));
						} else {
							$text = sprintf($this->injector['Lang']->t('system', 'uninstall_following_modules_first'), implode(', ', $deps));
						}
					} else {
						$text = $this->injector['Lang']->t('system', 'module_installer_dot_found');
					}
				} else {
					$text = $this->injector['Lang']->t('system', 'protected_module_description');
				}
				Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
				break;
			default:
				Core\Functions::getRedirectMessage();

				// Languagecache neu erstellen, für den Fall, dass neue Module hinzugefügt wurden
				$this->injector['Lang']->setLanguageCache();

				$modules = Core\Modules::getAllModules();
				$installed_modules = $new_modules = array();

				foreach ($modules as $key => $values) {
					if ($this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'modules WHERE name = ?', array($values['dir'])) == 1) {
						$installed_modules[$key] = $values;
					} else {
						$new_modules[$key] = $values;
					}
				}

				$this->injector['View']->assign('installed_modules', $installed_modules);
				$this->injector['View']->assign('new_modules', $new_modules);
		}
	}

	public function actionSql_export()
	{
		$this->injector['Breadcrumb']
				->append($this->injector['Lang']->t('system', 'acp_maintenance'), $this->injector['URI']->route('acp/system/maintenance'))
				->append($this->injector['Lang']->t('system', 'acp_sql_export'));

		if (isset($_POST['submit']) === true) {
			if (empty($_POST['tables']) || is_array($_POST['tables']) === false)
				$errors['tables'] = $this->injector['Lang']->t('system', 'select_sql_tables');
			if ($_POST['output'] !== 'file' && $_POST['output'] !== 'text')
				$errors[] = $this->injector['Lang']->t('system', 'select_output');
			if (in_array($_POST['export_type'], array('complete', 'structure', 'data')) === false)
				$errors[] = $this->injector['Lang']->t('system', 'select_export_type');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$this->injector['Session']->unsetFormToken();

				$structure = '';
				$data = '';
				foreach ($_POST['tables'] as $table) {
					// Struktur ausgeben
					if ($_POST['export_type'] === 'complete' || $_POST['export_type'] === 'structure') {
						$result = $this->injector['Db']->fetchAssoc('SHOW CREATE TABLE ' . $table);
						if (!empty($result)) {
							//$structure.= '-- ' . sprintf($this->injector['Lang']->t('system', 'structure_of_table'), $table) . "\n\n";
							$structure.= isset($_POST['drop']) && $_POST['drop'] == 1 ? 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n" : '';
							$structure.= $result['Create Table'] . ';' . "\n\n";
						}
					}

					// Datensätze ausgeben
					if ($_POST['export_type'] === 'complete' || $_POST['export_type'] === 'data') {
						$resultsets = $this->injector['Db']->fetchAll('SELECT * FROM ' . DB_PRE . substr($table, strlen(CONFIG_DB_PRE)));
						if (count($resultsets) > 0) {
							//$data.= "\n" . '-- '. sprintf($this->injector['Lang']->t('system', 'data_of_table'), $table) . "\n\n";
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
					$this->injector['View']->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
				}
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$db_tables = $this->injector['Db']->fetchAll('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_TYPE = ? AND TABLE_SCHEMA = ?', array('BASE TABLE', CONFIG_DB_NAME));
			$tables = array();
			foreach ($db_tables as $row) {
				$table = $row['TABLE_NAME'];
				if (strpos($table, CONFIG_DB_PRE) === 0) {
					$tables[$table]['name'] = $table;
					$tables[$table]['selected'] = Core\Functions::selectEntry('tables', $table);
				}
			}
			ksort($tables);
			$this->injector['View']->assign('tables', $tables);

			// Ausgabe
			$lang_output = array($this->injector['Lang']->t('system', 'output_as_file'), $this->injector['Lang']->t('system', 'output_as_text'));
			$this->injector['View']->assign('output', Core\Functions::selectGenerator('output', array('file', 'text'), $lang_output, 'file', 'checked'));

			// Exportart
			$lang_export_type = array(
				$this->injector['Lang']->t('system', 'complete_export'),
				$this->injector['Lang']->t('system', 'export_structure'),
				$this->injector['Lang']->t('system', 'export_data')
			);
			$this->injector['View']->assign('export_type', Core\Functions::selectGenerator('export_type', array('complete', 'structure', 'data'), $lang_export_type, 'complete', 'checked'));

			$drop = array();
			$drop['checked'] = Core\Functions::selectEntry('drop', '1', '', 'checked');
			$drop['lang'] = $this->injector['Lang']->t('system', 'drop_tables');
			$this->injector['View']->assign('drop', $drop);

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionSql_import()
	{
		$this->injector['Breadcrumb']
				->append($this->injector['Lang']->t('system', 'acp_maintenance'), $this->injector['URI']->route('acp/system/maintenance'))
				->append($this->injector['Lang']->t('system', 'acp_sql_import'));

		if (isset($_POST['submit']) === true) {
			if (isset($_FILES['file'])) {
				$file['tmp_name'] = $_FILES['file']['tmp_name'];
				$file['name'] = $_FILES['file']['name'];
				$file['size'] = $_FILES['file']['size'];
			}

			if (empty($_POST['text']) && empty($file['size']))
				$errors['text'] = $this->injector['Lang']->t('system', 'type_in_text_or_select_sql_file');
			if (!empty($file['size']) &&
					(!Core\Validate::mimeType($file['tmp_name'], 'text/plain') ||
					$_FILES['file']['error'] !== UPLOAD_ERR_OK))
				$errors['file'] = $this->injector['Lang']->t('system', 'select_sql_file');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$this->injector['Session']->unsetFormToken();

				$data = isset($file) ? file_get_contents($file['tmp_name']) : $_POST['text'];
				$data_ary = explode(";\n", str_replace(array("\r\n", "\r", "\n"), "\n", $data));
				$sql_queries = array();

				$i = 0;
				foreach ($data_ary as $row) {
					if (!empty($row)) {
						$bool = $this->injector['Db']->query($row);
						$sql_queries[$i]['query'] = str_replace("\n", '<br />', $row);
						$sql_queries[$i]['color'] = $bool !== null ? '090' : 'f00';
						++$i;

						if (!$bool) {
							break;
						}
					}
				}

				$this->injector['View']->assign('sql_queries', $sql_queries);

				Core\Cache::purge();
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('text' => ''));

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionUpdate_check()
	{
		$this->injector['Breadcrumb']->append($this->injector['Lang']->t('system', 'acp_maintenance'), $this->injector['URI']->route('acp/system/maintenance'))
				->append($this->injector['Lang']->t('system', 'acp_update_check'));

		$file = @file_get_contents('http://www.acp3-cms.net/update.txt');
		if ($file !== false) {
			$data = explode('||', $file);
			if (count($data) === 2) {
				$update = array(
					'installed_version' => CONFIG_VERSION,
					'current_version' => $data[0],
				);

				if (version_compare($update['installed_version'], $update['current_version'], '>=')) {
					$update['text'] = $this->injector['Lang']->t('system', 'acp3_up_to_date');
					$update['class'] = 'success';
				} else {
					$update['text'] = sprintf($this->injector['Lang']->t('system', 'acp3_not_up_to_date'), '<a href="' . $data[1] . '" onclick="window.open(this.href); return false">', '</a>');
					$update['class'] = 'error';
				}

				$this->injector['View']->assign('update', $update);
			}
		}
	}

}