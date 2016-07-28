<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Controller\Install;

use ACP3\Core\Filesystem;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\XML;
use ACP3\Installer\Core;
use ACP3\Installer\Core\Date;
use ACP3\Installer\Modules\Install\Controller\AbstractAction;
use ACP3\Installer\Modules\Install\Model\InstallModel;
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
     * @var XML
     */
    protected $xml;
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    protected $dateHelper;
    /**
     * @var Forms
     */
    protected $forms;
    /**
     * @var InstallModel
     */
    protected $installModel;
    /**
     * @var \ACP3\Installer\Modules\Install\Validation\FormValidation
     */
    protected $formValidation;

    /**
     * @param \ACP3\Installer\Core\Controller\Context\InstallerContext $context
     * @param \ACP3\Installer\Core\Date $date
     * @param XML $xml
     * @param \ACP3\Core\Helpers\Date $dateHelper
     * @param Forms $forms
     * @param InstallModel $installModel
     * @param \ACP3\Installer\Modules\Install\Validation\FormValidation $formValidation
     */
    public function __construct(
        Core\Controller\Context\InstallerContext $context,
        Date $date,
        XML $xml,
        \ACP3\Core\Helpers\Date $dateHelper,
        Forms $forms,
        InstallModel $installModel,
        FormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->xml = $xml;
        $this->installModel = $installModel;
        $this->dateHelper = $dateHelper;
        $this->forms = $forms;
        $this->formValidation = $formValidation;
        $this->configFilePath = $this->appPath->getAppDir() . 'config.yml';
    }

    public function execute()
    {
        if ($this->request->getPost()->count() > 0 && !$this->request->getPost()->get('languages')) {
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
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'designs' => $this->getAvailableDesigns()
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

            $this->installModel->writeConfigFile($this->configFilePath, $formData);
            $this->installModel->updateContainer($this->request);
            $this->installModel->installModules();
            $this->installModel->installAclResources();
            $this->installModel->createSuperUser($formData);

            if (isset($formData['sample_data']) && $formData['sample_data'] == 1) {
                $this->installModel->installSampleData();
            }

            $this->installModel->configureModules($formData);

            $this->setTemplate('install/install.result.tpl');
        } catch (ValidationFailedException $e) {
            return $this->renderErrorBoxOnFailedFormValidation($e);
        } catch (\Exception $e) {
            $this->get('core.logger')->error('installer', $e->getMessage());
            $this->get('core.logger')->error('installer', $e->getTraceAsString());
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
        if ($this->request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => false, 'content' => $errors]);
        }

        return ['error_msg' => $errors];
    }

    /**
     * @return array
     */
    private function getAvailableDesigns()
    {
        $designs = [];
        $path = ACP3_ROOT_DIR . 'designs/';
        $directories = Filesystem::scandir($path);
        foreach ($directories as $directory) {
            $designInfo = $this->xml->parseXmlFile($path . $directory . '/info.xml', '/design');
            if (!empty($designInfo)) {
                $designs[] = array_merge(
                    $designInfo,
                    [
                        'selected' => $this->forms->selectEntry('design', $directory),
                        'dir' => $directory
                    ]
                );
            }
        }

        return $designs;
    }
}
