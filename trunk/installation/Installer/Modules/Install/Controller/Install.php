<?php

namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Core\Config;
use ACP3\Installer\Core\Date;
use ACP3\Core\Exceptions\ValidationFailed;
use ACP3\Core\Helpers\Secure;
use ACP3\Installer\Core;
use ACP3\Installer\Modules\Install\Helpers;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class Install
 * @package ACP3\Installer\Modules\Install\Controller
 */
class Install extends AbstractController
{
    const ACP3_VERSION = '4.0-dev';
    /**
     * @var string
     */
    protected $configFilePath = '';

    /**
     * @var Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Helpers
     */
    protected $installHelper;

    /**
     * @param Core\Context $context
     * @param Date $date
     * @param Helpers $installHelper
     */
    public function __construct(
        Core\Context $context,
        Date $date,
        Helpers $installHelper
    )
    {
        parent::__construct($context);

        $this->date = $date;
        $this->installHelper = $installHelper;
        $this->configFilePath = ACP3_DIR . 'config/config.php';
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $this->_indexPost($_POST);
        }

        // Zeitzonen
        $this->view->assign('time_zones', $this->date->getTimeZones(date_default_timezone_get()));

        $defaults = array(
            'db_host' => 'localhost',
            'db_pre' => 'acp3_',
            'db_user' => '',
            'db_name' => '',
            'user_name' => 'admin',
            'mail' => '',
            'date_format_long' => $this->date->getDateFormatLong(),
            'date_format_short' => $this->date->getDateFormatShort(),
            'seo_title' => 'ACP3',
        );

        $this->view->assign('form', array_merge($defaults, $_POST));
    }

    /**
     * @param array $formData
     */
    private function _indexPost(array $formData)
    {
        try {
            $validator = $this->get('install.validator');
            $validator->validateConfiguration($formData, $this->configFilePath);

            $this->_initDatabase($formData);
            $this->_setContainer();
            $bool = $this->_installModules();

            // Admin-User, Menüpunkte, News, etc. in die DB schreiben
            if ($bool === true) {
                $this->_installSampleData($formData);
            }

            $this->setTemplate('install/install.result.tpl');
            return;
        } catch (ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    private function _initDatabase(array $formData)
    {
        // Systemkonfiguration erstellen
        $configParams = array(
            'db_host' => $formData['db_host'],
            'db_name' => $formData['db_name'],
            'db_pre' => $formData['db_pre'],
            'db_password' => $formData['db_password'],
            'db_user' => $formData['db_user'],
        );

        $this->installHelper->writeConfigFile($this->configFilePath, $configParams);

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

        define('DB_PRE', $formData['db_pre']);

        $this->db = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    }

    /**
     * @throws \Exception
     */
    private function _setContainer()
    {
        $this->container = new ContainerBuilder();

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load(ACP3_DIR . 'config/services.yml');
        $loader->load(INSTALLER_ACP3_DIR . 'config/services.yml');
        $loader->load(INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/services.yml');

        // Load installer modules services
        $installerModules = array_diff(scandir(INSTALLER_MODULES_DIR), array('.', '..'));
        foreach ($installerModules as $module) {
            $path = INSTALLER_MODULES_DIR . $module . '/config/services.yml';
            if (is_file($path) === true) {
                $loader->load($path);
            }
        }

        $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));
        foreach ($modules as $module) {
            $path = MODULES_DIR . $module . '/config/services.yml';
            if (is_file($path) === true) {
                $loader->load($path);
            }
        }

        $this->container->set('core.db', $this->db);

        $params = array(
            'compile_id' => 'installer',
            'plugins_dir' => INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/',
            'template_dir' => array(DESIGN_PATH_INTERNAL, INSTALLER_MODULES_DIR)
        );
        $this->container->get('core.view')->setRenderer('smarty', $params);

        $this->container->compile();
    }

    /**
     * @return bool
     */
    private function _installModules()
    {
        $bool = false;
        // Install core modules
        $installFirst = array('system', 'permissions', 'users');
        foreach ($installFirst as $module) {
            $bool = $this->installHelper->installModule($module, $this->container);
            if ($bool === false) {
                $this->view->assign('install_error', true);
                break;
            }
        }

        // Install "normal" modules
        if ($bool === true) {
            // Systemeinstellungen laden
            $this
                ->get('system.config')
                ->getSettingsAsConstants();

            $modules = array_diff(scandir(MODULES_DIR), array('.', '..'));

            foreach ($modules as $module) {
                $module = strtolower($module);
                if (in_array(strtolower($module), $installFirst) === false) {
                    if ($this->installHelper->installModule($module, $this->container) === false) {
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
        $securityHelper = $this->get('core.helpers.secure');
        $salt = $securityHelper->salt(12);
        $currentDate = gmdate('Y-m-d H:i:s');

        $newsModuleId = $this->db->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array('news'));
        $queries = array(
            "INSERT INTO `{pre}users` VALUES ('', 1, " . $this->db->quote($formData["user_name"]) . ", '" . $securityHelper->generateSaltedPassword($salt, $formData["user_pwd"]) . ":" . $salt . "', 0, '', '1', '', 0, '" . $formData["mail"] . "', 0, '', '', '', '', '', '', '', '', 0, 0, " . $this->db->quote($formData["date_format_long"]) . ", " . $this->db->quote($formData["date_format_short"]) . ", '" . $formData["date_time_zone"] . "', '" . LANG . "', '20', '', '" . $currentDate . "');",
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

        if ($this->installHelper->executeSqlQueries($queries, $this->db) === false) {
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
            'homepage' => 'news/index/index/',
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
            'version' => self::ACP3_VERSION,
            'wysiwyg' => 'CKEditor'
        );

        $configSystem = new Config($this->db, 'system');
        $configSystem->setSettings($systemSettings);

        $configUsers = new Config($this->db, 'users');
        $configUsers->setSettings(array('mail' => $formData['mail']));

        $configContact = new Config($this->db, 'contact');
        $configContact->setSettings(array('mail' => $formData['mail'], 'disclaimer' => $this->lang->t('install', 'disclaimer')));

        $configNewsletter = new Config($this->db, 'newsletter');
        $configNewsletter->setSettings(array('mail' => $formData['mail'], 'mailsig' => $this->lang->t('install', 'sincerely') . "\n\n" . $this->lang->t('install', 'newsletter_mailsig')));
    }

}