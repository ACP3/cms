<?php

namespace ACP3\Installer\Modules\Install\Controller;

use ACP3\Installer\Core;
use ACP3\Installer\Modules\Install\Helpers\Requirements;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Install\Controller
 */
class Index extends AbstractController
{
    /**
     * @var \ACP3\Installer\Modules\Install\Helpers\Requirements
     */
    protected $requirementsHelpers;

    /**
     * @param \ACP3\Installer\Core\Modules\Controller\Context                         $context
     * @param \ACP3\Installer\Modules\Install\Helpers\Requirements $requirementsHelpers
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
        Requirements $requirementsHelpers)
    {
        parent::__construct($context);

        $this->requirementsHelpers = $requirementsHelpers;
    }

    public function actionRequirements()
    {
        list($requirements, $stopInstall) = $this->requirementsHelpers->checkMandatoryRequirements();
        list($requiredFilesAndDirs, $checkAgain) = $this->requirementsHelpers->checkFolderAndFilePermissions();

        $this->view->assign('requirements', $requirements);
        $this->view->assign('files_dirs', $requiredFilesAndDirs);
        $this->view->assign('php_settings', $this->requirementsHelpers->checkOptionalRequirements());
        $this->view->assign('stop_install', $stopInstall);
        $this->view->assign('check_again', $checkAgain);
    }

    public function actionLicence()
    {
        return;
    }

    public function actionIndex()
    {
        return;
    }

}
