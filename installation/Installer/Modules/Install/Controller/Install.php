<?php

namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Core\Auth;
use ACP3\Core\Filesystem;
use ACP3\Core\Functions;
use ACP3\Core\Helpers\Secure;
use ACP3\Installer\Core\Date;
use ACP3\Core\Exceptions\ValidationFailed;
use ACP3\Installer\Core;
use ACP3\Installer\Modules\Install\Helpers\Install as InstallerHelpers;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class Install
 * @package ACP3\Installer\Modules\Install\Controller
 */
class Install extends AbstractController
{
    /**
     * @var string
     */
    protected $configFilePath = '';

    /**
     * @var \ACP3\Installer\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\DB
     */
    protected $db;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Installer\Modules\Install\Helpers\Install
     */
    protected $installHelper;

    /**
     * @param \ACP3\Installer\Core\Modules\Controller\Context $context
     * @param \ACP3\Installer\Core\Date                       $date
     * @param \ACP3\Core\Helpers\Secure                       $secureHelper
     * @param \ACP3\Installer\Modules\Install\Helpers\Install $installHelper
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Date $date,
        Secure $secureHelper,
        InstallerHelpers $installHelper
    )
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->installHelper = $installHelper;
        $this->configFilePath = ACP3_DIR . 'config.yml';
    }

    public function actionIndex()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_indexPost($this->request->getPost()->getAll());
        }

        $this->view->assign('time_zones', $this->get('core.helpers.date')->getTimeZones(date_default_timezone_get()));

        $defaults = [
            'db_host' => 'localhost',
            'db_pre' => 'acp3_',
            'db_user' => '',
            'db_name' => '',
            'user_name' => 'admin',
            'mail' => '',
            'date_format_long' => $this->date->getDateFormatLong(),
            'date_format_short' => $this->date->getDateFormatShort(),
            'title' => 'ACP3',
        ];

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));
    }

    /**
     * @param array $formData
     */
    private function _indexPost(array $formData)
    {
        try {
            $validator = $this->get('install.validator');
            $validator->validateConfiguration($formData, $this->configFilePath);

            $this->_writeConfigFile($formData);
            $this->_setContainer();
            $resultModules = $this->_installModules();
            $resultAcl = false;

            if ($resultModules === true) {
                $resultAcl = $this->_installAclResources();
            }

            // Admin-User, Menüpunkte, News, etc. in die DB schreiben
            if ($resultModules === true && $resultAcl === true) {
                $this->_installSampleData($formData);
                $this->configureModules($formData);
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
    private function _writeConfigFile(array $formData)
    {
        // Systemkonfiguration erstellen
        $configParams = [
            'parameters' => [
                'db_host' => $formData['db_host'],
                'db_name' => $formData['db_name'],
                'db_table_prefix' => $formData['db_pre'],
                'db_password' => $formData['db_password'],
                'db_user' => $formData['db_user'],
                'db_driver' => 'pdo_mysql',
                'db_charset' => 'utf8'
            ]
        ];

        $this->installHelper->writeConfigFile($this->configFilePath, $configParams);
    }

    /**
     * @throws \Exception
     */
    private function _setContainer()
    {
        $this->container = new ContainerBuilder();

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load(CLASSES_DIR . 'config/services.yml');
        $loader->load(INSTALLER_CLASSES_DIR . 'config/services.yml');
        $loader->load(INSTALLER_CLASSES_DIR . 'View/Renderer/Smarty/config/services.yml');

        // Load installer modules services
        $installerModules = array_diff(scandir(INSTALLER_MODULES_DIR), ['.', '..']);
        foreach ($installerModules as $module) {
            $path = INSTALLER_MODULES_DIR . $module . '/config/services.yml';
            if (is_file($path) === true) {
                $loader->load($path);
            }
        }

        $modules = array_diff(scandir(MODULES_DIR . 'ACP3/'), ['.', '..']);
        foreach ($modules as $module) {
            $path = MODULES_DIR . 'ACP3/' . $module . '/config/services.yml';
            if (is_file($path) === true) {
                $loader->load($path);
            }
        }

        $this->container->setParameter('cache_driver', 'Array');

        $this->container->compile();
    }

    /**
     * @return bool
     */
    private function _installModules()
    {
        $bool = false;
        // Install core modules
        $installFirst = ['system', 'permissions', 'users'];
        foreach ($installFirst as $module) {
            $bool = $this->installHelper->installModule($module, $this->container);
            if ($bool === false) {
                $this->view->assign('install_error', true);
                break;
            }
        }

        // Install "normal" modules
        if ($bool === true) {
            foreach (Filesystem::scandir(MODULES_DIR . 'ACP3/') as $module) {
                $module = strtolower($module);
                if (in_array(strtolower($module), $installFirst) === false) {
                    $bool = $this->installHelper->installModule($module, $this->container);
                    if ($bool === false) {
                        $this->view->assign('install_error', true);
                        break;
                    }
                }
            }
        }

        return $bool;
    }

    /**
     * @return bool
     */
    private function _installAclResources()
    {
        $bool = false;
        foreach (Filesystem::scandir(MODULES_DIR . 'ACP3/') as $module) {
            $bool = $this->installHelper->installResources($module, $this->container);
            if ($bool === false) {
                $this->view->assign('install_error', true);
                break;
            }
        }

        return $bool;
    }

    /**
     * @param array $formData
     */
    private function _installSampleData(array $formData)
    {
        $bool = $this->createSuperUser($formData);

        // Install module sample data
        $bool2 = $this->installModuleSampleData();

        if ($bool === false || $bool2 === false) {
            $this->view->assign('install_error', true);
        }
    }

    /**
     * Set the module settings
     *
     * @param array $formData
     */
    private function configureModules(array $formData)
    {
        $settings = [
            'system' => [
                'date_format_long' => Functions::strEncode($formData['date_format_long']),
                'date_format_short' => Functions::strEncode($formData['date_format_short']),
                'date_time_zone' => $formData['date_time_zone'],
                'maintenance_message' => $this->lang->t('install', 'offline_message'),
                'lang' => $this->lang->getLanguage()
            ],
            'seo' => [
                'title' => !empty($formData['title']) ? $formData['title'] : 'ACP3'
            ],
            'users' => [
                'mail' => $formData['mail']
            ],
            'newsletter' => [
                'mail' => $formData['mail'],
                'mailsig' => $this->lang->t('install', 'sincerely') . "\n\n" . $this->lang->t('install', 'newsletter_mailsig')
            ],
            'contact' => [
                'mail' => $formData['mail'],
                'disclaimer' => $this->lang->t('install', 'disclaimer')
            ]
        ];

        foreach ($settings as $module => $data) {
            $this->get('core.config')->setSettings($data, $module);
        }
    }

    /**
     * @param array $formData
     *
     * @return bool
     */
    private function createSuperUser(array $formData)
    {
        /** @var \ACP3\Core\DB db */
        $this->db = $this->get('core.db');

        $salt = $this->secureHelper->salt(Auth::SALT_LENGTH);
        $currentDate = gmdate('Y-m-d H:i:s');

        $queries = [
            "INSERT INTO
                `{pre}users`
            VALUES
                ('', 1, {$this->db->getConnection()->quote($formData["user_name"])}, '{$this->secureHelper->generateSaltedPassword($salt, $formData["user_pwd"], 'sha512')}', '{$salt}', '', 0, '', '1', '', 0, '{$formData["mail"]}', 0, '', '', '', '', '', '', '', '', 0, 0, {$this->db->getConnection()->quote($formData["date_format_long"])}, {$this->db->getConnection()->quote($formData["date_format_short"])}, '{$formData["date_time_zone"]}', '{$this->lang->getLanguage()}', '20', '', '{$currentDate}');",
        ];

        return $this->get('core.modules.schemaHelper')->executeSqlQueries($queries);
    }

    /**
     * @return bool
     */
    private function installModuleSampleData()
    {
        $modules = array_diff(scandir(MODULES_DIR . 'ACP3/'), ['.', '..']);

        foreach ($modules as $module) {
            $module = strtolower($module);

            if ($this->installHelper->installSampleData($module, $this->container, $this->get('core.modules.schemaHelper')) === false) {
                return false;
            }
        }

        return true;
    }
}
