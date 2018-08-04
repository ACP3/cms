<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Install\Model;

use ACP3\Core\Helpers\Secure;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules\Vendor;
use ACP3\Installer\Core\DependencyInjection\ServiceContainerBuilder;
use ACP3\Installer\Core\Environment\ApplicationPath;
use ACP3\Installer\Core\I18n\Translator;
use ACP3\Installer\Modules\Install\Helpers\Install;
use ACP3\Installer\Modules\Install\Helpers\ModuleInstaller;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstallModel
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var Install
     */
    protected $installHelper;
    /**
     * @var ApplicationPath
     */
    protected $appPath;
    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;
    /**
     * @var ModuleInstaller
     */
    protected $moduleInstaller;
    /**
     * @var Vendor
     */
    protected $vendor;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * InstallModel constructor.
     *
     * @param LoggerInterface    $logger
     * @param ContainerInterface $container
     * @param ApplicationPath    $appPath
     * @param Vendor             $vendor
     * @param Secure             $secure
     * @param Translator         $translator
     * @param Install            $installHelper
     * @param ModuleInstaller    $moduleInstaller
     */
    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container,
        ApplicationPath $appPath,
        Vendor $vendor,
        Secure $secure,
        Translator $translator,
        Install $installHelper,
        ModuleInstaller $moduleInstaller
    ) {
        $this->container = $container;
        $this->appPath = $appPath;
        $this->vendor = $vendor;
        $this->secure = $secure;
        $this->translator = $translator;
        $this->installHelper = $installHelper;
        $this->moduleInstaller = $moduleInstaller;
        $this->logger = $logger;
    }

    /**
     * @param string $configFilePath
     * @param array  $formData
     */
    public function writeConfigFile($configFilePath, array $formData)
    {
        $configParams = [
            'parameters' => [
                'db_host' => $formData['db_host'],
                'db_name' => $formData['db_name'],
                'db_table_prefix' => $formData['db_pre'],
                'db_password' => $formData['db_password'],
                'db_user' => $formData['db_user'],
                'db_driver' => 'pdo_mysql',
                'db_charset' => 'utf8mb4',
            ],
        ];

        $this->installHelper->writeConfigFile($configFilePath, $configParams);
    }

    /**
     * @param RequestInterface $request
     *
     * @throws \Exception
     */
    public function updateContainer(RequestInterface $request)
    {
        $this->container = ServiceContainerBuilder::create(
            $this->logger,
            $this->appPath,
            $request->getSymfonyRequest(),
            $this->container->getParameter('core.environment'),
            true,
            true
        );
    }

    /**
     * @throws \Exception
     */
    public function installModules()
    {
        $this->moduleInstaller->installModules(
            $this->container,
            $this->container->get('core.installer.schema_registrar')->all()
        );
    }

    /**
     * @throws \Exception
     */
    public function installAclResources()
    {
        foreach ($this->container->get('core.installer.schema_registrar')->all() as $schema) {
            if ($this->installHelper->installResources($schema, $this->container) === false) {
                throw new \Exception(
                    \sprintf('Error while installing ACL resources for the module %s.', $schema->getModuleName())
                );
            }
        }
    }

    /**
     * Set the module settings.
     *
     * @param array $formData
     */
    public function configureModules(array $formData)
    {
        $settings = [
            Schema::MODULE_NAME => [
                'date_format_long' => $this->secure->strEncode($formData['date_format_long']),
                'date_format_short' => $this->secure->strEncode($formData['date_format_short']),
                'date_time_zone' => $formData['date_time_zone'],
                'maintenance_message' => $this->translator->t('install', 'offline_message'),
                'lang' => $this->translator->getLocale(),
                'design' => $formData['design'],
                'site_title' => !empty($formData['title']) ? $formData['title'] : 'ACP3',
            ],
            \ACP3\Modules\ACP3\Users\Installer\Schema::MODULE_NAME => [
                'mail' => $formData['mail'],
            ],
        ];

        foreach ($settings as $module => $data) {
            $this->container->get('core.config')->saveSettings($data, $module);
        }
    }

    /**
     * @param array $formData
     *
     * @throws \Exception
     */
    public function createSuperUser(array $formData)
    {
        /* @var \ACP3\Core\Database\Connection db */
        $this->db = $this->container->get('core.db');

        $salt = $this->secure->salt(UserModel::SALT_LENGTH);
        $currentDate = \gmdate('Y-m-d H:i:s');

        $queries = [
            "INSERT INTO
                `{pre}users`
            VALUES
                (1, 1, {$this->db->getConnection()->quote($formData['user_name'])}, '{$this->secure->generateSaltedPassword($salt, $formData['user_pwd'], 'sha512')}', '{$salt}', '', 0, '', '1', '', 0, '{$formData['mail']}', 0, '', '', '', '', '', '', '', '', 0, 0, '{$currentDate}');",
            'INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (1, 4);',
        ];

        if ($this->container->get('core.modules.schemaHelper')->executeSqlQueries($queries) === false) {
            throw new \Exception('Error while creating the super user.');
        }
    }

    /**
     * @throws \Exception
     */
    public function installSampleData()
    {
        foreach ($this->container->get('core.installer.sample_data_registrar')->all() as $sampleData) {
            $sampleDataInstallResult = $this->installHelper->installSampleData(
                $sampleData,
                $this->container->get('core.modules.schemaHelper')
            );

            if ($sampleDataInstallResult === false) {
                throw new \Exception('Error while installing module sample data.');
            }
        }
    }
}
