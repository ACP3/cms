<?php

namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Core\Exceptions\ValidationFailed;
use ACP3\Core\Filesystem;
use ACP3\Core\Functions;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\User;
use ACP3\Installer\Core;
use ACP3\Installer\Core\Date;
use ACP3\Installer\Modules\Install\Helpers\Install as InstallerHelpers;
use ACP3\Installer\Modules\Install\Validation\FormValidation;
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
     * @var \ACP3\Core\Helpers\Date
     */
    protected $dateHelper;
    /**
     * @var \ACP3\Installer\Modules\Install\Helpers\Install
     */
    protected $installHelper;
    /**
     * @var \ACP3\Installer\Modules\Install\Validation\FormValidation
     */
    protected $formValidation;

    /**
     * @param \ACP3\Installer\Core\Modules\Controller\InstallerContext  $context
     * @param \ACP3\Installer\Core\Date                                 $date
     * @param \ACP3\Core\Helpers\Secure                                 $secureHelper
     * @param \ACP3\Core\Helpers\Date                                   $dateHelper
     * @param \ACP3\Installer\Modules\Install\Helpers\Install           $installHelper
     * @param \ACP3\Installer\Modules\Install\Validation\FormValidation $formValidation
     */
    public function __construct(
        Core\Modules\Controller\InstallerContext $context,
        Date $date,
        Secure $secureHelper,
        \ACP3\Core\Helpers\Date $dateHelper,
        InstallerHelpers $installHelper,
        FormValidation $formValidation
    )
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->dateHelper = $dateHelper;
        $this->installHelper = $installHelper;
        $this->formValidation = $formValidation;
        $this->configFilePath = $this->appPath->getAppDir() . 'config.yml';
    }

    public function actionIndex()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_indexPost($this->request->getPost()->all());
        }

        $this->view->assign('time_zones', $this->dateHelper->getTimeZones(date_default_timezone_get()));

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

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->all()));
    }

    /**
     * @param array $formData
     */
    private function _indexPost(array $formData)
    {
        try {
            $this->formValidation
                ->setConfigFilePath($this->configFilePath)
                ->validate($formData);

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
        } catch (\Exception $e) {
            $this->setTemplate('install/install.error.tpl');
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

        $this->container->set('core.environment.application_path', $this->appPath);
        $this->container->setParameter('core.environment', $this->container->getParameter('core.environment'));

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__));
        $loader->load($this->appPath->getClassesDir() . 'config/services.yml');
        $loader->load($this->appPath->getInstallerClassesDir() . 'config/services.yml');

        $modulesServices = glob($this->appPath->getModulesDir() . 'ACP3/*/Resources/config/services.yml');
        foreach ($modulesServices as $moduleServices) {
            $loader->load($moduleServices);
        }

        $this->container->setParameter('cache_driver', 'Array');

        $this->container->compile();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function _installModules()
    {
        $bool = false;
        $modules = array_merge(['system', 'users'], Filesystem::scandir($this->appPath->getModulesDir() . 'ACP3/'));
        $alreadyInstalled = [];

        foreach ($modules as $module) {
            $module = strtolower($module);
            if (!in_array($module, $alreadyInstalled)) {
                $bool = $this->installHelper->installModule($module, $this->container);
                $alreadyInstalled[] = $module;
                if ($bool === false) {
                    throw new \Exception("Error while installing module {$module}.");
                }
            }
        }

        return $bool;
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    private function _installAclResources()
    {
        $bool = false;
        foreach (Filesystem::scandir($this->appPath->getModulesDir() . 'ACP3/') as $module) {
            $bool = $this->installHelper->installResources($module, $this->container);
            if ($bool === false) {
                throw new \Exception("Error while installing ACL resources of the module {$module}.");
            }
        }

        return $bool;
    }

    /**
     * @param array $formData
     *
     * @throws \Exception
     */
    private function _installSampleData(array $formData)
    {
        $bool = $this->createSuperUser($formData);

        // Install module sample data
        $bool2 = $this->installModuleSampleData();

        if ($bool === false || $bool2 === false) {
            throw new \Exception("Error while installing module sample data.");
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
                'maintenance_message' => $this->translator->t('install', 'offline_message'),
                'lang' => $this->translator->getLocale()
            ],
            'seo' => [
                'title' => !empty($formData['title']) ? $formData['title'] : 'ACP3'
            ],
            'users' => [
                'mail' => $formData['mail']
            ],
            'newsletter' => [
                'mail' => $formData['mail'],
                'mailsig' => $this->translator->t('install', 'sincerely') . "\n\n" . $this->translator->t('install',
                        'newsletter_mailsig')
            ],
            'contact' => [
                'mail' => $formData['mail'],
                'disclaimer' => $this->translator->t('install', 'disclaimer')
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

        $salt = $this->secureHelper->salt(User::SALT_LENGTH);
        $currentDate = gmdate('Y-m-d H:i:s');

        $queries = [
            "INSERT INTO
                `{pre}users`
            VALUES
                (1, 1, {$this->db->getConnection()->quote($formData["user_name"])}, '{$this->secureHelper->generateSaltedPassword($salt, $formData["user_pwd"], 'sha512')}', '{$salt}', '', 0, '', '1', '', 0, '{$formData["mail"]}', 0, '', '', '', '', '', '', '', '', 0, 0, {$this->db->getConnection()->quote($formData["date_format_long"])}, {$this->db->getConnection()->quote($formData["date_format_short"])}, '{$formData["date_time_zone"]}', '{$this->translator->getLocale()}', '20', '', '{$currentDate}');",
            "INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (1, 4);"
        ];

        return $this->get('core.modules.schemaHelper')->executeSqlQueries($queries);
    }

    /**
     * @return bool
     */
    private function installModuleSampleData()
    {
        foreach (Filesystem::scandir($this->appPath->getModulesDir() . 'ACP3/') as $module) {
            $module = strtolower($module);

            if ($this->installHelper->installSampleData($module, $this->container, $this->get('core.modules.schemaHelper')) === false) {
                return false;
            }
        }

        return true;
    }
}
