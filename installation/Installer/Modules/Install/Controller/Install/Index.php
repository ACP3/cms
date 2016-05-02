<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Controller\Install;

use ACP3\Core\Filesystem;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\User;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Installer\Core;
use ACP3\Installer\Core\Date;
use ACP3\Installer\Modules\Install\Controller\AbstractAction;
use ACP3\Installer\Modules\Install\Helpers\Install as InstallerHelpers;
use ACP3\Installer\Modules\Install\Validation\FormValidation;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Install\Controller\Install
 */
class Index extends AbstractAction
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
     * @var \ACP3\Core\Database\Connection
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
     * @param \ACP3\Installer\Core\Controller\Context\InstallerContext $context
     * @param \ACP3\Installer\Core\Date $date
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param \ACP3\Core\Helpers\Date $dateHelper
     * @param \ACP3\Installer\Modules\Install\Helpers\Install $installHelper
     * @param \ACP3\Installer\Modules\Install\Validation\FormValidation $formValidation
     */
    public function __construct(
        Core\Controller\Context\InstallerContext $context,
        Date $date,
        Secure $secureHelper,
        \ACP3\Core\Helpers\Date $dateHelper,
        InstallerHelpers $installHelper,
        FormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->dateHelper = $dateHelper;
        $this->installHelper = $installHelper;
        $this->formValidation = $formValidation;
        $this->configFilePath = $this->appPath->getAppDir() . 'config.yml';
    }

    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false && !$this->request->getPost()->get('languages')) {
            return $this->executePost($this->request->getPost()->all());
        }

        $defaults = [
            'db_host' => 'localhost',
            'db_pre' => '',
            'db_user' => '',
            'db_name' => '',
            'user_name' => '',
            'mail' => '',
            'date_format_long' => $this->date->getDateFormatLong(),
            'date_format_short' => $this->date->getDateFormatShort(),
            'title' => 'ACP3',
        ];

        return [
            'time_zones' => $this->dateHelper->getTimeZones(date_default_timezone_get()),
            'form' => array_merge($defaults, $this->request->getPost()->all())
        ];
    }

    /**
     * @param array $formData
     * @return array|JsonResponse
     */
    private function executePost(array $formData)
    {
        try {
            $this->formValidation
                ->setConfigFilePath($this->configFilePath)
                ->validate($formData);

            $this->writeConfigFile($formData);
            $this->updateContainer();
            $this->installModules();
            $this->installAclResources();
            $this->installSampleData($formData);
            $this->configureModules($formData);

            $this->setTemplate('install/install.result.tpl');
        } catch (ValidationFailedException $e) {
            return $this->renderErrorBoxOnFailedFormValidation($e);
        } catch (\Exception $e) {
            $this->setTemplate('install/install.error.tpl');
        }
    }

    /**
     * @param \Exception $exception
     * @return array|JsonResponse
     */
    private function renderErrorBoxOnFailedFormValidation(\Exception $exception)
    {
        $errors = $this->get('core.helpers.alerts')->errorBox($exception->getMessage());
        if ($this->request->isAjax()) {
            return new JsonResponse(['success' => false, 'content' => $errors]);
        }

        return ['error_msg' => $errors];
    }

    /**
     * @param array $formData
     */
    private function writeConfigFile(array $formData)
    {
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
    private function updateContainer()
    {
        $this->container = Core\ServiceContainerBuilder::compileContainer(
            $this->container->getParameter('core.environment'),
            $this->appPath,
            true
        );
    }

    /**
     * @throws \Exception
     */
    private function installModules()
    {
        $modules = array_merge(['system', 'users'], Filesystem::scandir($this->appPath->getModulesDir() . 'ACP3/'));
        $alreadyInstalled = [];

        foreach ($modules as $module) {
            $module = strtolower($module);
            if (!in_array($module, $alreadyInstalled)) {
                if ($this->installHelper->installModule($module, $this->container) === false) {
                    throw new \Exception("Error while installing module {$module}.");
                }

                $alreadyInstalled[] = $module;
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function installAclResources()
    {
        foreach (Filesystem::scandir($this->appPath->getModulesDir() . 'ACP3/') as $module) {
            if ($this->installHelper->installResources($module, $this->container) === false) {
                throw new \Exception("Error while installing ACL resources for the module {$module}.");
            }
        }
    }

    /**
     * @param array $formData
     *
     * @throws \Exception
     */
    private function installSampleData(array $formData)
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
                'date_format_long' => $this->get('core.helpers.secure')->strEncode($formData['date_format_long']),
                'date_format_short' => $this->get('core.helpers.secure')->strEncode($formData['date_format_short']),
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
        /** @var \ACP3\Core\Database\Connection db */
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
            $sampleDataInstallResult = $this->installHelper->installSampleData(
                $module,
                $this->container,
                $this->get('core.modules.schemaHelper')
            );

            if ($sampleDataInstallResult === false) {
                return false;
            }
        }

        return true;
    }
}
