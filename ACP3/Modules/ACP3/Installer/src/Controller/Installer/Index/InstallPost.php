<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Index;

use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Helpers\Alerts;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;
use ACP3\Modules\ACP3\Installer\Helpers\Navigation;
use ACP3\Modules\ACP3\Installer\Model\InstallModel;
use ACP3\Modules\ACP3\Installer\Validation\FormValidation;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class InstallPost extends AbstractAction implements InvokableActionInterface
{
    /**
     * @var InstallModel
     */
    private $installModel;
    /**
     * @var \ACP3\Modules\ACP3\Installer\Validation\FormValidation
     */
    private $formValidation;
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
        InstallModel $installModel,
        FormValidation $formValidation
    ) {
        parent::__construct($context, $navigation);

        $this->installModel = $installModel;
        $this->formValidation = $formValidation;
        $this->alertsHelper = $alertsHelper;
        $this->logger = $logger;
    }

    /**
     * @return array|JsonResponse|null
     */
    public function __invoke()
    {
        try {
            $formData = $this->request->getPost()->all();

            $configFilePath = $this->appPath->getAppDir() . 'config.yml';

            $this->formValidation
                ->setConfigFilePath($configFilePath)
                ->validate($formData);

            $this->installModel->writeConfigFile($configFilePath, $formData);
            $this->installModel->updateContainer();
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

        return null;
    }

    /**
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
}
