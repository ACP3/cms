<?php

namespace ACP3\Modules\System\Controller;

use ACP3\Core;
use ACP3\Modules\System;

/**
 * Description of SystemAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{
    /**
     *
     * @var Model
     */
    protected $model;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view, $seo);

        $this->model = new System\Model($this->db, $this->lang);
    }

    public function actionConfiguration()
    {
        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateSettings($_POST);

                // Config aktualisieren
                $config = array(
                    'cache_images' => (int)$_POST['cache_images'],
                    'cache_minify' => (int)$_POST['cache_minify'],
                    'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
                    'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
                    'date_time_zone' => $_POST['date_time_zone'],
                    'entries' => (int)$_POST['entries'],
                    'extra_css' => $_POST['extra_css'],
                    'extra_js' => $_POST['extra_js'],
                    'flood' => (int)$_POST['flood'],
                    'homepage' => $_POST['homepage'],
                    'icons_path' => $_POST['icons_path'],
                    'mailer_smtp_auth' => (int)$_POST['mailer_smtp_auth'],
                    'mailer_smtp_host' => $_POST['mailer_smtp_host'],
                    'mailer_smtp_password' => $_POST['mailer_smtp_password'],
                    'mailer_smtp_port' => (int)$_POST['mailer_smtp_port'],
                    'mailer_smtp_security' => $_POST['mailer_smtp_security'],
                    'mailer_smtp_user' => $_POST['mailer_smtp_user'],
                    'mailer_type' => $_POST['mailer_type'],
                    'maintenance_message' => $_POST['maintenance_message'],
                    'maintenance_mode' => (int)$_POST['maintenance_mode'],
                    'seo_aliases' => (int)$_POST['seo_aliases'],
                    'seo_meta_description' => Core\Functions::strEncode($_POST['seo_meta_description']),
                    'seo_meta_keywords' => Core\Functions::strEncode($_POST['seo_meta_keywords']),
                    'seo_mod_rewrite' => (int)$_POST['seo_mod_rewrite'],
                    'seo_robots' => (int)$_POST['seo_robots'],
                    'seo_title' => Core\Functions::strEncode($_POST['seo_title']),
                    'wysiwyg' => $_POST['wysiwyg']
                );

                $bool = Core\Config::setSettings('system', $config);

                // Gecachete Stylesheets und JavaScript Dateien löschen
                if (CONFIG_EXTRA_CSS !== $_POST['extra_css'] ||
                    CONFIG_EXTRA_JS !== $_POST['extra_js']
                ) {
                    Core\Cache::purge('minify');
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'config_edit_success' : 'config_edit_error'), 'acp/system/configuration');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/system/configuration');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        Core\Functions::getRedirectMessage();

        $this->view->assign('entries', Core\Functions::recordsPerPage(CONFIG_ENTRIES));

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
        $this->view->assign('wysiwyg', $wysiwyg);

        // Zeitzonen
        $this->view->assign('time_zones', Core\Date::getTimeZones(CONFIG_DATE_TIME_ZONE));

        // Wartungsmodus an/aus
        $lang_maintenance = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('maintenance', Core\Functions::selectGenerator('maintenance_mode', array(1, 0), $lang_maintenance, CONFIG_MAINTENANCE_MODE, 'checked'));

        // Robots
        $lang_robots = array(
            $this->lang->t('system', 'seo_robots_index_follow'),
            $this->lang->t('system', 'seo_robots_index_nofollow'),
            $this->lang->t('system', 'seo_robots_noindex_follow'),
            $this->lang->t('system', 'seo_robots_noindex_nofollow')
        );
        $this->view->assign('robots', Core\Functions::selectGenerator('seo_robots', array(1, 2, 3, 4), $lang_robots, CONFIG_SEO_ROBOTS));

        // URI-Aliases aktivieren/deaktivieren
        $lang_aliases = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('aliases', Core\Functions::selectGenerator('seo_aliases', array(1, 0), $lang_aliases, CONFIG_SEO_ALIASES, 'checked'));

        // Sef-URIs
        $lang_mod_rewrite = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('mod_rewrite', Core\Functions::selectGenerator('seo_mod_rewrite', array(1, 0), $lang_mod_rewrite, CONFIG_SEO_MOD_REWRITE, 'checked'));

        // Caching von Bildern
        $lang_cache_images = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('cache_images', Core\Functions::selectGenerator('cache_images', array(1, 0), $lang_cache_images, CONFIG_CACHE_IMAGES, 'checked'));

        // Mailertyp
        $lang_mailer_type = array($this->lang->t('system', 'mailer_type_php_mail'), $this->lang->t('system', 'mailer_type_smtp'));
        $this->view->assign('mailer_type', Core\Functions::selectGenerator('mailer_type', array('mail', 'smtp'), $lang_mailer_type, CONFIG_MAILER_TYPE));

        // Mailer SMTP Authentifizierung
        $lang_mailer_smtp_auth = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('mailer_smtp_auth', Core\Functions::selectGenerator('mailer_smtp_auth', array(1, 0), $lang_mailer_smtp_auth, CONFIG_MAILER_SMTP_AUTH, 'checked'));

        // Mailer SMTP Verschlüsselung
        $lang_mailer_smtp_security = array(
            $this->lang->t('system', 'mailer_smtp_security_none'),
            $this->lang->t('system', 'mailer_smtp_security_ssl'),
            $this->lang->t('system', 'mailer_smtp_security_tls')
        );
        $this->view->assign('mailer_smtp_security', Core\Functions::selectGenerator('mailer_smtp_security', array('none', 'ssl', 'tls'), $lang_mailer_smtp_security, CONFIG_MAILER_SMTP_SECURITY));

        $settings = Core\Config::getSettings('system');

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

        $this->session->generateFormToken();
    }

    public function actionDesigns()
    {
        $this->breadcrumb
            ->append($this->lang->t('system', 'acp_extensions'), $this->uri->route('acp/system/extensions'))
            ->append($this->lang->t('system', 'acp_designs'));

        if (isset($this->uri->dir)) {
            $bool = false;

            if ((bool)preg_match('=/=', $this->uri->dir) === false &&
                is_file(ACP3_ROOT_DIR . 'designs/' . $this->uri->dir . '/info.xml') === true
            ) {
                $bool = Core\Config::setSettings('system', array('design' => $this->uri->dir));

                // Template Cache leeren
                Core\Cache::purge('tpl_compiled');
                Core\Cache::purge('tpl_cached');
            }
            $text = $this->lang->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

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
            $this->view->assign('designs', $designs);
        }
    }

    public function actionExtensions()
    {
        $this->view->setContentTemplate('system/acp_extensions.tpl');
    }

    public function actionLanguages()
    {
        $this->breadcrumb
            ->append($this->lang->t('system', 'acp_extensions'), $this->uri->route('acp/system/extensions'))
            ->append($this->lang->t('system', 'acp_languages'));

        if (isset($this->uri->dir)) {
            $bool = false;

            if ($this->lang->languagePackExists($this->uri->dir) === true) {
                $bool = Core\Config::setSettings('system', array('lang' => $this->uri->dir));
                $this->lang->setLanguage($this->uri->dir);
            }
            $text = $this->lang->t('system', $bool === true ? 'languages_edit_success' : 'languages_edit_error');

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
            $this->view->assign('languages', $languages);
        }
    }

    public function actionList()
    {
        $this->view->setContentTemplate('system/acp_list.tpl');
    }

    public function actionMaintenance()
    {
        $this->view->setContentTemplate('system/acp_maintenance.tpl');
    }

    public function actionModules()
    {
        $this->breadcrumb
            ->append($this->lang->t('system', 'acp_extensions'), $this->uri->route('acp/system/extensions'))
            ->append($this->lang->t('system', 'acp_modules'));

        switch ($this->uri->action) {
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
                $this->lang->setLanguageCache();

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

                $this->view->assign('installed_modules', $installed_modules);
                $this->view->assign('new_modules', $new_modules);
        }
    }

    protected function enableModule()
    {
        $bool = false;
        $info = Core\Modules::getModuleInfo($this->uri->dir);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            $bool = $this->db->update(DB_PRE . 'modules', array('active' => 1), array('name' => $this->uri->dir));
            Core\Modules::setModulesCache();
            Core\ACL::setResourcesCache();

            $text = $this->lang->t('system', 'mod_activate_' . ($bool !== false ? 'success' : 'error'));
        }
        Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
    }

    protected function disableModule()
    {
        $bool = false;
        $info = Core\Modules::getModuleInfo($this->uri->dir);
        if (empty($info)) {
            $text = $this->lang->t('system', 'module_not_found');
        } elseif ($info['protected'] === true) {
            $text = $this->lang->t('system', 'mod_deactivate_forbidden');
        } else {
            // Modulabhängigkeiten prüfen
            $deps = System\Helpers::checkUninstallDependencies($this->uri->dir);

            if (empty($deps)) {
                $bool = $this->db->update(DB_PRE . 'modules', array('active' => 0), array('name' => $this->uri->dir));
                Core\Modules::setModulesCache();
                Core\ACL::setResourcesCache();

                $text = $this->lang->t('system', 'mod_deactivate_' . ($bool !== false ? 'success' : 'error'));
            } else {
                $text = sprintf($this->lang->t('system', 'module_disable_not_possible'), implode(', ', $deps));
            }
        }
        Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
    }

    protected function installModule()
    {
        $bool = false;
        // Nur noch nicht installierte Module berücksichtigen
        if (Core\Modules::isInstalled($this->uri->dir) === false) {
            $mod_name = ucfirst($this->uri->dir);
            $path = MODULES_DIR . $mod_name . '/Installer.php';
            if (is_file($path) === true) {
                // Modulabhängigkeiten prüfen
                $deps = System\Helpers::checkInstallDependencies($this->uri->dir);

                // Modul installieren
                if (empty($deps)) {
                    $className = Core\Modules\AbstractInstaller::buildClassName($this->uri->dir);
                    $install = new $className();
                    $bool = $install->install();
                    Core\Modules::setModulesCache();
                    $text = $this->lang->t('system', 'mod_installation_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'enable_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->lang->t('system', 'module_already_installed');
        }
        Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
    }

    protected function uninstallModule()
    {
        $bool = false;
        $mod_info = Core\Modules::getModuleInfo($this->uri->dir);
        // Nur installierte und Nicht-Core-Module berücksichtigen
        if ($mod_info['protected'] === false && Core\Modules::isInstalled($this->uri->dir) === true) {
            $mod_name = ucfirst($this->uri->dir);
            $path = MODULES_DIR . $mod_name . '/Installer.php';
            if (is_file($path) === true) {
                // Modulabhängigkeiten prüfen
                $deps = System\Helpers::checkUninstallDependencies($this->uri->dir);

                // Modul deinstallieren
                if (empty($deps)) {
                    $className = Core\Modules\AbstractInstaller::buildClassName($this->uri->dir);
                    $install = new $className();
                    $bool = $install->uninstall();
                    Core\Modules::setModulesCache();
                    $text = $this->lang->t('system', 'mod_uninstallation_' . ($bool !== false ? 'success' : 'error'));
                } else {
                    $text = sprintf($this->lang->t('system', 'uninstall_following_modules_first'), implode(', ', $deps));
                }
            } else {
                $text = $this->lang->t('system', 'module_installer_not_found');
            }
        } else {
            $text = $this->lang->t('system', 'protected_module_description');
        }
        Core\Functions::setRedirectMessage($bool, $text, 'acp/system/modules');
    }

    public function actionSqlExport()
    {
        $this->breadcrumb
            ->append($this->lang->t('system', 'acp_maintenance'), $this->uri->route('acp/system/maintenance'))
            ->append($this->lang->t('system', 'acp_sql_export'));

        if (isset($_POST['submit']) === true) {
            if (empty($_POST['tables']) || is_array($_POST['tables']) === false)
                $errors['tables'] = $this->lang->t('system', 'select_sql_tables');
            if ($_POST['output'] !== 'file' && $_POST['output'] !== 'text')
                $errors[] = $this->lang->t('system', 'select_output');
            if (in_array($_POST['export_type'], array('complete', 'structure', 'data')) === false)
                $errors[] = $this->lang->t('system', 'select_export_type');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } elseif (Core\Validate::formToken() === false) {
                $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
            } else {
                $this->session->unsetFormToken();

                $structure = '';
                $data = '';
                foreach ($_POST['tables'] as $table) {
                    // Struktur ausgeben
                    if ($_POST['export_type'] === 'complete' || $_POST['export_type'] === 'structure') {
                        $result = $this->db->fetchAssoc('SHOW CREATE TABLE ' . $table);
                        if (!empty($result)) {
                            $structure .= isset($_POST['drop']) && $_POST['drop'] == 1 ? 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n\n" : '';
                            $structure .= $result['Create Table'] . ';' . "\n\n";
                        }
                    }

                    // Datensätze ausgeben
                    if ($_POST['export_type'] === 'complete' || $_POST['export_type'] === 'data') {
                        $resultsets = $this->db->fetchAll('SELECT * FROM ' . DB_PRE . substr($table, strlen(CONFIG_DB_PRE)));
                        if (count($resultsets) > 0) {
                            $fields = '';
                            // Felder der jeweiligen Tabelle auslesen
                            foreach (array_keys($resultsets[0]) as $field) {
                                $fields .= '`' . $field . '`, ';
                            }

                            // Datensätze auslesen
                            foreach ($resultsets as $row) {
                                $values = '';
                                foreach ($row as $value) {
                                    $values .= '\'' . $value . '\', ';
                                }
                                $data .= 'INSERT INTO `' . $table . '` (' . substr($fields, 0, -2) . ') VALUES (' . substr($values, 0, -2) . ');' . "\n";
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
                    $this->view->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
                }
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            $dbTables = $this->model->getSchemaTables();
            $tables = array();
            foreach ($dbTables as $row) {
                $table = $row['TABLE_NAME'];
                if (strpos($table, CONFIG_DB_PRE) === 0) {
                    $tables[$table]['name'] = $table;
                    $tables[$table]['selected'] = Core\Functions::selectEntry('tables', $table);
                }
            }
            ksort($tables);
            $this->view->assign('tables', $tables);

            // Ausgabe
            $lang_output = array($this->lang->t('system', 'output_as_file'), $this->lang->t('system', 'output_as_text'));
            $this->view->assign('output', Core\Functions::selectGenerator('output', array('file', 'text'), $lang_output, 'file', 'checked'));

            // Exportart
            $lang_export_type = array(
                $this->lang->t('system', 'complete_export'),
                $this->lang->t('system', 'export_structure'),
                $this->lang->t('system', 'export_data')
            );
            $this->view->assign('export_type', Core\Functions::selectGenerator('export_type', array('complete', 'structure', 'data'), $lang_export_type, 'complete', 'checked'));

            $drop = array();
            $drop['checked'] = Core\Functions::selectEntry('drop', '1', '', 'checked');
            $drop['lang'] = $this->lang->t('system', 'drop_tables');
            $this->view->assign('drop', $drop);

            $this->session->generateFormToken();
        }
    }

    public function actionSqlImport()
    {
        $this->breadcrumb
            ->append($this->lang->t('system', 'acp_maintenance'), $this->uri->route('acp/system/maintenance'))
            ->append($this->lang->t('system', 'acp_sql_import'));

        if (isset($_POST['submit']) === true) {
            if (isset($_FILES['file'])) {
                $file['tmp_name'] = $_FILES['file']['tmp_name'];
                $file['name'] = $_FILES['file']['name'];
                $file['size'] = $_FILES['file']['size'];
            }

            if (empty($_POST['text']) && empty($file['size']))
                $errors['text'] = $this->lang->t('system', 'type_in_text_or_select_sql_file');
            if (!empty($file['size']) &&
                (!Core\Validate::mimeType($file['tmp_name'], 'text/plain') ||
                    $_FILES['file']['error'] !== UPLOAD_ERR_OK)
            )
                $errors['file'] = $this->lang->t('system', 'select_sql_file');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } elseif (Core\Validate::formToken() === false) {
                $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
            } else {
                $this->session->unsetFormToken();

                $data = isset($file) ? file_get_contents($file['tmp_name']) : $_POST['text'];
                $data_ary = explode(";\n", str_replace(array("\r\n", "\r", "\n"), "\n", $data));
                $sql_queries = array();

                $i = 0;
                foreach ($data_ary as $row) {
                    if (!empty($row)) {
                        $bool = $this->db->query($row);
                        $sql_queries[$i]['query'] = str_replace("\n", '<br />', $row);
                        $sql_queries[$i]['color'] = $bool !== null ? '090' : 'f00';
                        ++$i;

                        if (!$bool) {
                            break;
                        }
                    }
                }

                $this->view->assign('sql_queries', $sql_queries);

                Core\Cache::purge();
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('text' => ''));

            $this->session->generateFormToken();
        }
    }

    public function actionUpdateCheck()
    {
        $this->breadcrumb
            ->append($this->lang->t('system', 'acp_maintenance'), $this->uri->route('acp/system/maintenance'))
            ->append($this->lang->t('system', 'acp_update_check'));

        $file = @file_get_contents('http://www.acp3-cms.net/update.txt');
        if ($file !== false) {
            $data = explode('||', $file);
            if (count($data) === 2) {
                $update = array(
                    'installed_version' => CONFIG_VERSION,
                    'current_version' => $data[0],
                );

                if (version_compare($update['installed_version'], $update['current_version'], '>=')) {
                    $update['text'] = $this->lang->t('system', 'acp3_up_to_date');
                    $update['class'] = 'success';
                } else {
                    $update['text'] = sprintf($this->lang->t('system', 'acp3_not_up_to_date'), '<a href="' . $data[1] . '" onclick="window.open(this.href); return false">', '</a>');
                    $update['class'] = 'error';
                }

                $this->view->assign('update', $update);
            }
        }
    }

}