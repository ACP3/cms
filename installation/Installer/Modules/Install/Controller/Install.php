<?php

namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Core\Config;
use ACP3\Core\Helpers\Secure;
use ACP3\Installer\Core;

/**
 * Module controller of the Install installer module
 *
 * @author Tino Goratsch
 */
class Install extends AbstractController
{

    public function actionIndex()
    {
        if (isset($_POST['submit'])) {
            $configPath = ACP3_DIR . 'config/config.php';

            if (empty($_POST['db_host'])) {
                $errors['db-host'] = $this->lang->t('install', 'type_in_db_host');
            }
            if (empty($_POST['db_user'])) {
                $errors['db-user'] = $this->lang->t('install', 'type_in_db_username');
            }
            if (empty($_POST['db_name'])) {
                $errors['db-name'] = $this->lang->t('install', 'type_in_db_name');
            }
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
                } catch (\Exception $e) {
                    $errors[] = sprintf($this->lang->t('install', 'db_connection_failed'), $e->getMessage());
                }
            }
            if (empty($_POST['user_name'])) {
                $errors['user-name'] = $this->lang->t('install', 'type_in_user_name');
            }
            if ((empty($_POST['user_pwd']) || empty($_POST['user_pwd_wdh'])) ||
                (!empty($_POST['user_pwd']) && !empty($_POST['user_pwd_wdh']) && $_POST['user_pwd'] != $_POST['user_pwd_wdh'])
            ) {
                $errors['user-pwd'] = $this->lang->t('install', 'type_in_pwd');
            }
            if (\ACP3\Core\Validate::email($_POST['mail']) === false) {
                $errors['mail'] = $this->lang->t('install', 'wrong_email_format');
            }
            if (empty($_POST['date_format_long'])) {
                $errors['date-format-long'] = $this->lang->t('install', 'type_in_date_format');
            }
            if (empty($_POST['date_format_short'])) {
                $errors['date-format-short'] = $this->lang->t('install', 'type_in_date_format');
            }
            if (\ACP3\Core\Validate::timeZone($_POST['date_time_zone']) === false) {
                $errors['date-time-zone'] = $this->lang->t('install', 'select_time_zone');
            }
            if (is_file($configPath) === false || is_writable($configPath) === false) {
                $errors[] = $this->lang->t('install', 'wrong_chmod_for_config_file');
            }

            if (isset($errors)) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } else {
                $this->_initDatabase($_POST);

                $bool = $this->_installModules();

                // Admin-User, Menüpunkte, News, etc. in die DB schreiben
                if ($bool === true) {
                    $this->_installSampleData($_POST);
                }

                $this->setContentTemplate('install/result.tpl');
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

            $this->view->assign('form', array_merge($defaults, $_POST));
        }
    }

    /**
     * @param array $formData
     */
    private function _initDatabase(array $formData)
    {
        // Systemkonfiguration erstellen
        $configFile = array(
            'db_host' => $formData['db_host'],
            'db_name' => $formData['db_name'],
            'db_pre' => $formData['db_pre'],
            'db_password' => $formData['db_password'],
            'db_user' => $formData['db_user'],
        );
        Core\Functions::writeConfigFile($configFile);

        // Doctrine DBAL Initialisieren
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'dbname' => $formData['db_name'],
            'user' => $formData['db_user'],
            'password' => $formData['db_password'],
            'host' => $formData['db_host'],
            'driver' => 'pdo_mysql',
            'charset' => 'utf8'
        );
        \ACP3\Core\Registry::set('Db', \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config));
        define('DB_PRE', $formData['db_pre']);

    }

    /**
     * @return bool
     */
    private function _installModules()
    {
        $bool = false;
        // Core-Module installieren
        $installFirst = array('system', 'permissions', 'users');
        foreach ($installFirst as $module) {
            $bool = Core\Functions::installModule($module);
            if ($bool === false) {
                $this->view->assign('install_error', true);
                break;
            }
        }

        // "Normale" Module installieren
        if ($bool === true) {
            $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
            foreach ($modules as $module) {
                if (in_array(strtolower($module), $installFirst) === false) {
                    $bool = Core\Functions::installModule($module);
                    if ($bool === false) {
                        $this->view->assign('install_error', true);
                        break;
                    }
                }
            }
        }

        return $bool;
    }

    private function _installSampleData(array $formData)
    {
        $securityHelper = new Secure();
        $salt = $securityHelper->salt(12);
        $currentDate = gmdate('Y-m-d H:i:s');

        $newsModuleId = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array('news'));
        $queries = array(
            "INSERT INTO `{pre}users` VALUES ('', 1, " . \ACP3\Core\Registry::get('Db')->quote($formData["user_name"]) . ", '" . $securityHelper->generateSaltedPassword($salt, $formData["user_pwd"]) . ":" . $salt . "', 0, '', '1', '', 0, '" . $formData["mail"] . "', 0, '', '', '', '', '', '', '', '', 0, 0, " . \ACP3\Core\Registry::get('Db')->quote($formData["date_format_long"]) . ", " . \ACP3\Core\Registry::get('Db')->quote($formData["date_format_short"]) . ", '" . $formData["date_time_zone"] . "', '" . LANG . "', '20', '', '" . $currentDate . "');",
            'INSERT INTO `{pre}categories` VALUES (\'\', \'' . $this->lang->t('install', 'category_name') . '\', \'\', \'' . $this->lang->t('install', 'category_description') . '\', \'' . $newsModuleId . '\');',
            'INSERT INTO `{pre}news` VALUES (\'\', \'' . $currentDate . '\', \'' . $currentDate . '\', \'' . $this->lang->t('install', 'news_headline') . '\', \'' . $this->lang->t('install', 'news_text') . '\', \'1\', \'1\', \'1\', \'\', \'\', \'\', \'\');',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 1, 0, 1, 4, 1, \'' . $this->lang->t('install', 'pages_news') . '\', \'news\', 1);',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 1, 1, 2, 3, 1, \'' . $this->lang->t('install', 'pages_newsletter') . '\', \'newsletter\', 1);',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 3, 0, 5, 6, 1, \'' . $this->lang->t('install', 'pages_files') . '\', \'files\', 1);',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 4, 0, 7, 8, 1, \'' . $this->lang->t('install', 'pages_gallery') . '\', \'gallery\', 1);',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 5, 0, 9, 10, 1, \'' . $this->lang->t('install', 'pages_guestbook') . '\', \'guestbook\', 1);',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 6, 0, 11, 12, 1, \'' . $this->lang->t('install', 'pages_polls') . '\', \'polls\', 1);',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 1, 7, 0, 13, 14, 1, \'' . $this->lang->t('install', 'pages_search') . '\', \'search\', 1);',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 1, 2, 8, 0, 15, 16, 1, \'' . $this->lang->t('install', 'pages_contact') . '\', \'contact\', 1);',
            'INSERT INTO `{pre}menu_items` VALUES (\'\', 2, 2, 9, 0, 17, 18, 1, \'' . $this->lang->t('install', 'pages_imprint') . '\', \'contact/index/imprint/\', 1);',
            'INSERT INTO `{pre}menus` VALUES (1, \'main\', \'' . $this->lang->t('install', 'pages_main') . '\');',
            'INSERT INTO `{pre}menus` VALUES (2, \'sidebar\', \'' . $this->lang->t('install', 'pages_sidebar') . '\');',
        );

        if (\ACP3\Core\Modules\AbstractInstaller::executeSqlQueries($queries) === false) {
            $this->view->assign('install_error', true);
        }

        // Modulkonfigurationsdateien schreiben
        $systemSettings = array(
            'cache_images' => true,
            'cache_minify' => 3600,
            'date_format_long' => \ACP3\Core\Functions::strEncode($formData['date_format_long']),
            'date_format_short' => \ACP3\Core\Functions::strEncode($formData['date_format_short']),
            'date_time_zone' => $formData['date_time_zone'],
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
            'maintenance_message' => $this->lang->t('install', 'offline_message'),
            'seo_meta_description' => '',
            'seo_meta_keywords' => '',
            'seo_mod_rewrite' => 1,
            'seo_robots' => 1,
            'seo_title' => !empty($formData['seo_title']) ? $formData['seo_title'] : 'ACP3',
            'version' => CONFIG_VERSION,
            'wysiwyg' => 'CKEditor'
        );

        $configSystem = new Config(\ACP3\Core\Registry::get('Db'), 'system');
        $configSystem->setSettings($systemSettings);

        $configUsers = new Config(\ACP3\Core\Registry::get('Db'), 'users');
        $configUsers->setSettings(array('mail' => $formData['mail']));

        $configContact = new Config(\ACP3\Core\Registry::get('Db'), 'contact');
        $configContact->setSettings(array('mail' => $formData['mail'], 'disclaimer' => $this->lang->t('install', 'disclaimer')));

        $configNewsletter = new Config(\ACP3\Core\Registry::get('Db'), 'newsletter');
        $configNewsletter->setSettings(array('mail' => $formData['mail'], 'mailsig' => $this->lang->t('install', 'sincerely') . "\n\n" . $this->lang->t('install', 'newsletter_mailsig')));

    }

}
