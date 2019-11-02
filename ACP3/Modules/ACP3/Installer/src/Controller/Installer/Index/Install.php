<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Index;

use ACP3\Core\Date;
use ACP3\Core\Helpers\Alerts;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\XML;
use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;
use ACP3\Modules\ACP3\Installer\Helpers\Navigation;
use ACP3\Modules\ACP3\Installer\Model\InstallModel;
use ACP3\Modules\ACP3\Installer\Validation\FormValidation;
use ACP3\Modules\ACP3\System\Helper\AvailableDesignsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Install extends AbstractAction
{
    use AvailableDesignsTrait;

    /**
     * @var \ACP3\Core\Date
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
     * @var \ACP3\Modules\ACP3\Installer\Validation\FormValidation
     */
    protected $formValidation;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        InstallerContext $context,
        LoggerInterface $logger,
        Alerts $alertsHelper,
        Navigation $navigation,
        Date $date,
        XML $xml,
        \ACP3\Core\Helpers\Date $dateHelper,
        Forms $forms,
        InstallModel $installModel,
        FormValidation $formValidation
    ) {
        parent::__construct($context, $navigation);

        $this->date = $date;
        $this->xml = $xml;
        $this->installModel = $installModel;
        $this->dateHelper = $dateHelper;
        $this->forms = $forms;
        $this->formValidation = $formValidation;
        $this->alertsHelper = $alertsHelper;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return [
            'time_zones' => $this->dateHelper->getTimeZones(\date_default_timezone_get()),
            'form' => \array_merge($this->getFormDefaults(), $this->request->getPost()->all()),
            'designs' => $this->getAvailableDesigns(),
        ];
    }

    /**
     * @return array
     */
    private function getFormDefaults()
    {
        return [
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
    }

    /**
     * @return array|JsonResponse
     */
    public function executePost()
    {
        try {
            $formData = $this->request->getPost()->all();

            $configFilePath = $this->appPath->getAppDir() . 'config.yml';

            $this->formValidation
                ->setConfigFilePath($configFilePath)
                ->validate($formData);

            $this->installModel->writeConfigFile($configFilePath, $formData);
            $this->installModel->updateContainer($this->request);
            $this->installModel->installModules();
            $this->installModel->installAclResources();
            $this->installModel->createSuperUser($formData);

            if (isset($formData['sample_data']) && $formData['sample_data'] == 1) {
                $this->installModel->installSampleData();
            }

            $this->installModel->configureModules($formData);

            $this->navigation->markStepComplete('index_install');

            $this->setTemplate('Installer/Installer/index.install.result.tpl');
        } catch (ValidationFailedException $e) {
            return $this->renderErrorBoxOnFailedFormValidation($e);
        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->setTemplate('Installer/Installer/index.install.error.tpl');
        }
    }

    /**
     * @param \Exception $exception
     *
     * @return array|Response
     */
    private function renderErrorBoxOnFailedFormValidation(\Exception $exception)
    {
        $errors = $this->alertsHelper->errorBox($exception->getMessage());
        if ($this->request->isXmlHttpRequest()) {
            return new Response($errors, 400);
        }

        return ['error_msg' => $errors];
    }

    /**
     * {@inheritdoc}
     */
    protected function getXml()
    {
        return $this->xml;
    }

    /**
     * {@inheritdoc}
     */
    protected function selectEntry($directory)
    {
        return $this->forms->selectEntry('design', $directory);
    }
}
