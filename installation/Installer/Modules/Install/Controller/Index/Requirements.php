<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Controller\Index;

use ACP3\Installer\Core;
use ACP3\Installer\Modules\Install\Controller\AbstractAction;
use ACP3\Installer\Modules\Install\Helpers\Requirements as RequirementsHelper;

/**
 * Class Requirements
 * @package ACP3\Installer\Modules\Install\Controller\Index
 */
class Requirements extends AbstractAction
{
    /**
     * @var \ACP3\Installer\Modules\Install\Helpers\Requirements
     */
    protected $requirementsHelpers;

    /**
     * Requirements constructor.
     *
     * @param \ACP3\Installer\Core\Controller\Context\InstallerContext $context
     * @param \ACP3\Installer\Modules\Install\Helpers\Requirements     $requirementsHelpers
     */
    public function __construct(
        Core\Controller\Context\InstallerContext $context,
        RequirementsHelper $requirementsHelpers)
    {
        parent::__construct($context);

        $this->requirementsHelpers = $requirementsHelpers;
    }

    public function execute()
    {
        list($requirements, $stopInstall) = $this->requirementsHelpers->checkMandatoryRequirements();
        list($requiredFilesAndDirs, $checkAgain) = $this->requirementsHelpers->checkFolderAndFilePermissions();

        $this->view->assign('requirements', $requirements);
        $this->view->assign('files_dirs', $requiredFilesAndDirs);
        $this->view->assign('php_settings', $this->requirementsHelpers->checkOptionalRequirements());
        $this->view->assign('stop_install', $stopInstall);
        $this->view->assign('check_again', $checkAgain);
    }
}
