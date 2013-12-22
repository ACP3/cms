<?php

namespace ACP3\Installer\Modules\Install;

use ACP3\Installer\Core;

/**
 * Module controller of the Install installer module
 *
 * @author Tino Goratsch
 */
class Install extends Core\Modules\Controller
{

    public function __construct()
    {
        parent::__construct();

        $navbar = array(
            'welcome' => array(
                'lang' => $this->lang->t('welcome'),
                'active' => false,
            ),
            'licence' => array(
                'lang' => $this->lang->t('licence'),
                'active' => false,
            ),
            'requirements' => array(
                'lang' => $this->lang->t('requirements'),
                'active' => false,
            ),
            'configuration' => array(
                'lang' => $this->lang->t('configuration'),
                'active' => false,
            )
        );
        if (isset($navbar[$this->uri->file]) === true) {
            $navbar[$this->uri->file]['active'] = true;
        }
        $this->view->assign('navbar', $navbar);
    }

    public function actionConfiguration()
    {
        if (isset($_POST['submit'])) {
            $config_path = ACP3_DIR . 'config.php';

            if (empty($_POST['db_host']))
                $errors['db-host'] = $this->lang->t('type_in_db_host');
            if (empty($_POST['db_user']))
                $errors['db-user'] = $this->lang->t('type_in_db_username');
            if (empty($_POST['db_name']))
                $errors['db-name'] = $this->lang->t('type_in_db_name');
            if (!empty($_POST['db_host']) && !empty($_POST['db_user']) && !empty($_POST['db_name'])) {
                try {
                    $config = new \Doctrine\DBAL\Configuration();

                    $connectionParams = array(
                        'dbname' => $_POST['db_name'],
                        'user' => $_POST['db_user'],
                        'password' => $_POST['db_password'],
                        'host' => $_POST['db_host'],
                        'driver' => 'pdo_mysql',
                        'charset' => 'utf8'
                    );
                    $db = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                    $db->query('USE `' . $_POST['db_name'] . '`');
                } catch (Exception $e) {
                    $errors[] = sprintf($this->lang->t('db_connection_failed'), $e->getMessage());
                }
            }
            if (empty($_POST['user_name']))
                $errors['user-name'] = $this->lang->t('type_in_user_name');
            if ((empty($_POST['user_pwd']) || empty($_POST['user_pwd_wdh'])) ||
                (!empty($_POST['user_pwd']) && !empty($_POST['user_pwd_wdh']) && $_POST['user_pwd'] != $_POST['user_pwd_wdh'])
            )
                $errors['user-pwd'] = $this->lang->t('type_in_pwd');
            if (\ACP3\Core\Validate::email($_POST['mail']) === false)
                $errors['mail'] = $this->lang->t('wrong_email_format');
            if (empty($_POST['date_format_long']))
                $errors['date-format-long'] = $this->lang->t('type_in_date_format');
            if (empty($_POST['date_format_short']))
                $errors['date-format-short'] = $this->lang->t('type_in_date_format');
            if (\ACP3\Core\Validate::timeZone($_POST['date_time_zone']) === false)
                $errors['date-time-zone'] = $this->lang->t('select_time_zone');
            if (is_file($config_path) === false || is_writable($config_path) === false)
                $errors[] = $this->lang->t('wrong_chmod_for_config_file');

            if (isset($errors)) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } else {
                // Systemkonfiguration erstellen
                $config_file = array(
                    'db_host' => $_POST['db_host'],
                    'db_name' => $_POST['db_name'],
                    'db_pre' => $_POST['db_pre'],
                    'db_password' => $_POST['db_password'],
                    'db_user' => $_POST['db_user'],
                );
                Core\Functions::writeConfigFile($config_file);

                // Doctrine DBAL Initialisieren
                $config = new \Doctrine\DBAL\Configuration();
                $connectionParams = array(
                    'dbname' => $_POST['db_name'],
                    'user' => $_POST['db_user'],
                    'password' => $_POST['db_password'],
                    'host' => $_POST['db_host'],
                    'driver' => 'pdo_mysql',
                    'charset' => 'utf8'
                );
                \ACP3\Core\Registry::set('Db', \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config));
                define('DB_PRE', $_POST['db_pre']);

                $bool = false;
                // Core-Module installieren
                $install_first = array('system', 'permissions', 'users');
                foreach ($install_first as $module) {
                    $bool = Core\Functions::installModule($module);
                    if ($bool === false) {
                        $this->view->assign('install_error', true);
                        break;
                    }
                }

                // "Normale" Module installieren
                if ($bool === true) {
                    $mods_dir = scandir(MODULES_DIR);
                    foreach ($mods_dir as $module) {
                        if ($module !== '.' && $module !== '..' && in_array(strtolower($module), $install_first) === false) {
                            $bool = Core\Functions::installModule($module);
                            if ($bool === false) {
                                $this->view->assign('install_error', true);
                                break;
                            }
                        }
                    }
                }

                // Admin-User, Menüpunkte, News, etc. in die DB schreiben
                if ($bool === true) {
                    $salt = \ACP3\Core\Functions::salt(12);
                    $current_date = gmdate('Y-m-d H:i:s');

                    $news_mod_id = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array('news'));
                    $queries = array(
                        "INSERT INTO `{pre}users` VALUES ('', 1, " . \ACP3\Core\Registry::get('Db')->quote($_POST["user_name"]) . ", '" . \ACP3\Core\Functions::generateSaltedPassword($salt, $_POST["user_pwd"]) . ":" . $salt . "', 0, '', '1', '', 0, '" . $_POST["mail"] . "', 0, '', '', '', '', '', '', '', '', 0, 0, " . \ACP3\Core\Registry::get('Db')->quote($_POST["date_format_long"]) . ", " . \ACP3\Core\Registry::get('Db')->quote($_POST["date_format_short"]) . ", '" . $_POST["date_time_zone"] . "', '" . LANG . "', '20', '', '" . $current_date . "');",
                        'INSERT INTO `{pre}categories` VALUES (\'\', \'' . $this->lang->t('category_name') . '\', \'\', \'' . $this->lang->t('category_description') . '\', \'' . $news_mod_id . '\');',
                        'INSERT INTO `{pre}news` VALUES (\'\', \'' . $current_date . '\', \'' . $current_date . '\', \'' . $this->lang->t('news_headline') . '\', \'' . $this->lang->t('news_text') . '\', \'1\', \'1\', \'1\', \'\', \'\', \'\', \'\');',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 1, 0, 1, 4, 1, \'' . $this->lang->t('pages_news') . '\', \'news\', 1);',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 1, 1, 2, 3, 1, \'' . $this->lang->t('pages_newsletter') . '\', \'newsletter\', 1);',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 3, 0, 5, 6, 1, \'' . $this->lang->t('pages_files') . '\', \'files\', 1);',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 4, 0, 7, 8, 1, \'' . $this->lang->t('pages_gallery') . '\', \'gallery\', 1);',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 5, 0, 9, 10, 1, \'' . $this->lang->t('pages_guestbook') . '\', \'guestbook\', 1);',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 6, 0, 11, 12, 1, \'' . $this->lang->t('pages_polls') . '\', \'polls\', 1);',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 7, 0, 13, 14, 1, \'' . $this->lang->t('pages_search') . '\', \'search\', 1);',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 2, 8, 0, 15, 16, 1, \'' . $this->lang->t('pages_contact') . '\', \'contact\', 1);',
                        'INSERT INTO `{pre}menu_items` VALUES (\'\', 2, 2, 9, 0, 17, 18, 1, \'' . $this->lang->t('pages_imprint') . '\', \'contact/imprint/\', 1);',
                        'INSERT INTO `{pre}menus` VALUES (1, \'main\', \'' . $this->lang->t('pages_main') . '\');',
                        'INSERT INTO `{pre}menus` VALUES (2, \'sidebar\', \'' . $this->lang->t('pages_sidebar') . '\');',
                    );

                    if (\ACP3\Core\Modules\AbstractInstaller::executeSqlQueries($queries) === false) {
                        $this->view->assign('install_error', true);
                    }

                    // Modulkonfigurationsdateien schreiben
                    $system_settings = array(
                        'cache_images' => true,
                        'cache_minify' => 3600,
                        'date_format_long' => \ACP3\Core\Functions::strEncode($_POST['date_format_long']),
                        'date_format_short' => \ACP3\Core\Functions::strEncode($_POST['date_format_short']),
                        'date_time_zone' => $_POST['date_time_zone'],
                        'design' => 'acp3',
                        'entries' => 20,
                        'flood' => 30,
                        'homepage' => 'news/list/',
                        'lang' => LANG,
                        'mailer_smtp_auth' => 0,
                        'mailer_smtp_host' => '',
                        'mailer_smtp_password' => '',
                        'mailer_smtp_port' => 25,
                        'mailer_smtp_security' => 'none',
                        'mailer_smtp_user' => '',
                        'mailer_type' => 'mail',
                        'maintenance_mode' => 0,
                        'maintenance_message' => $this->lang->t('offline_message'),
                        'seo_aliases' => 1,
                        'seo_meta_description' => '',
                        'seo_meta_keywords' => '',
                        'seo_mod_rewrite' => 1,
                        'seo_robots' => 1,
                        'seo_title' => !empty($_POST['seo_title']) ? $_POST['seo_title'] : 'ACP3',
                        'version' => CONFIG_VERSION,
                        'wysiwyg' => 'CKEditor'
                    );
                    \ACP3\Core\Config::setSettings('system', $system_settings);
                    \ACP3\Core\Config::setSettings('users', array('mail' => $_POST['mail']));
                    \ACP3\Core\Config::setSettings('contact', array('mail' => $_POST['mail'], 'disclaimer' => $this->lang->t('disclaimer')));
                    \ACP3\Core\Config::setSettings('newsletter', array('mail' => $_POST['mail'], 'mailsig' => $this->lang->t('sincerely') . "\n\n" . $this->lang->t('newsletter_mailsig')));
                }

                $this->view->setContentTemplate('install/result.tpl');
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            // Zeitzonen
            $this->view->assign('time_zones', \ACP3\Core\Date::getTimeZones(date_default_timezone_get()));

            $defaults = array(
                'db_host' => 'localhost',
                'db_pre' => 'acp3_',
                'db_user' => '',
                'db_name' => '',
                'user_name' => 'admin',
                'mail' => '',
                'date_format_long' => 'd.m.y, H:i',
                'date_format_short' => 'd.m.y',
                'seo_title' => 'ACP3',
            );

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);
        }
    }

    public function actionRequirements()
    {
        define('REQUIRED_PHP_VERSION', '5.3.2');
        define('COLOR_ERROR', 'f00');
        define('COLOR_SUCCESS', '090');
        define('CLASS_ERROR', 'important');
        define('CLASS_SUCCESS', 'success');
        define('CLASS_WARNING', 'warning');

        // Allgemeine Voraussetzungen
        $requirements = array();
        $requirements[0]['name'] = $this->lang->t('php_version');
        $requirements[0]['color'] = version_compare(phpversion(), REQUIRED_PHP_VERSION, '>=') ? COLOR_SUCCESS : COLOR_ERROR;
        $requirements[0]['found'] = phpversion();
        $requirements[0]['required'] = REQUIRED_PHP_VERSION;
        $requirements[1]['name'] = $this->lang->t('pdo_extension');
        $requirements[1]['color'] = extension_loaded('pdo') && extension_loaded('pdo_mysql') ? COLOR_SUCCESS : COLOR_ERROR;
        $requirements[1]['found'] = $this->lang->t($requirements[1]['color'] == COLOR_SUCCESS ? 'on' : 'off');
        $requirements[1]['required'] = $this->lang->t('on');
        $requirements[2]['name'] = $this->lang->t('gd_library');
        $requirements[2]['color'] = extension_loaded('gd') ? COLOR_SUCCESS : COLOR_ERROR;
        $requirements[2]['found'] = $this->lang->t($requirements[2]['color'] == COLOR_SUCCESS ? 'on' : 'off');
        $requirements[2]['required'] = $this->lang->t('on');
        $requirements[3]['name'] = $this->lang->t('register_globals');
        $requirements[3]['color'] = ((bool)ini_get('register_globals')) ? COLOR_ERROR : COLOR_SUCCESS;
        $requirements[3]['found'] = $this->lang->t(((bool)ini_get('register_globals')) ? 'on' : 'off');
        $requirements[3]['required'] = $this->lang->t('off');
        $requirements[4]['name'] = $this->lang->t('safe_mode');
        $requirements[4]['color'] = ((bool)ini_get('safe_mode')) ? COLOR_ERROR : COLOR_SUCCESS;
        $requirements[4]['found'] = $this->lang->t(((bool)ini_get('safe_mode')) ? 'on' : 'off');
        $requirements[4]['required'] = $this->lang->t('off');

        $this->view->assign('requirements', $requirements);

        $defaults = array('ACP3/config.php');

        // Uploadordner
        $uploads = scandir(UPLOADS_DIR);
        foreach ($uploads as $row) {
            $path = 'uploads/' . $row . '/';
            if ($row !== '.' && $row !== '..' && is_dir(ACP3_ROOT_DIR . $path) === true) {
                $defaults[] = $path;
            }
        }
        $requiredFilesAndDirs = array();
        $checkAgain = false;

        $i = 0;
        foreach ($defaults as $row) {
            $requiredFilesAndDirs[$i]['path'] = $row;
            // Überprüfen, ob es eine Datei oder ein Ordner ist
            if (is_file(ACP3_ROOT_DIR . $row) === true) {
                $requiredFilesAndDirs[$i]['class_1'] = CLASS_SUCCESS;
                $requiredFilesAndDirs[$i]['exists'] = $this->lang->t('found');
            } elseif (is_dir(ACP3_ROOT_DIR . $row) === true) {
                $requiredFilesAndDirs[$i]['class_1'] = CLASS_SUCCESS;
                $requiredFilesAndDirs[$i]['exists'] = $this->lang->t('found');
            } else {
                $requiredFilesAndDirs[$i]['class_1'] = CLASS_ERROR;
                $requiredFilesAndDirs[$i]['exists'] = $this->lang->t('not_found');
            }
            $requiredFilesAndDirs[$i]['class_2'] = is_writable(ACP3_ROOT_DIR . $row) === true ? CLASS_SUCCESS : CLASS_ERROR;
            $requiredFilesAndDirs[$i]['writable'] = $requiredFilesAndDirs[$i]['class_2'] === CLASS_SUCCESS ? $this->lang->t('writable') : $this->lang->t('not_writable');
            if ($requiredFilesAndDirs[$i]['class_1'] == CLASS_ERROR || $requiredFilesAndDirs[$i]['class_2'] == CLASS_ERROR) {
                $checkAgain = true;
            }
            ++$i;
        }
        $this->view->assign('files_dirs', $requiredFilesAndDirs);

        // PHP Einstellungen
        $phpSettings = array();
        $phpSettings[0]['setting'] = $this->lang->t('maximum_uploadsize');
        $phpSettings[0]['class'] = ini_get('post_max_size') > 0 ? CLASS_SUCCESS : CLASS_WARNING;
        $phpSettings[0]['value'] = ini_get('post_max_size');
        $phpSettings[1]['setting'] = $this->lang->t('magic_quotes');
        $phpSettings[1]['class'] = (bool)ini_get('magic_quotes_gpc') ? CLASS_WARNING : CLASS_SUCCESS;
        $phpSettings[1]['value'] = $this->lang->t((bool)ini_get('magic_quotes_gpc') ? 'on' : 'off');
        $this->view->assign('php_settings', $phpSettings);

        foreach ($requirements as $row) {
            if ($row['color'] !== COLOR_SUCCESS) {
                $this->view->assign('stop_install', true);
            }
        }

        if ($checkAgain === true) {
            $this->view->assign('check_again', true);
        }
    }

    public function actionLicence()
    {
        $this->view->setContentTemplate('install/licence.tpl');
    }

    public function actionWelcome()
    {
        $this->view->setContentTemplate('install/welcome.tpl');
    }

}
