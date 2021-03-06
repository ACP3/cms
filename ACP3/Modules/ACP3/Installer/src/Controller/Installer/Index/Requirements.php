<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Index;

use ACP3\Modules\ACP3\Installer\Core\Controller\Context\InstallerContext;
use ACP3\Modules\ACP3\Installer\Helpers\Navigation;
use ACP3\Modules\ACP3\Installer\Helpers\Requirements as RequirementsHelper;

class Requirements extends AbstractAction
{
    /**
     * @var \ACP3\Modules\ACP3\Installer\Helpers\Requirements
     */
    private $requirementsHelpers;

    public function __construct(
        InstallerContext $context,
        Navigation $navigation,
        RequirementsHelper $requirementsHelpers
    ) {
        parent::__construct($context, $navigation);

        $this->requirementsHelpers = $requirementsHelpers;
    }

    public function execute(): array
    {
        [$requirements, $stopInstall] = $this->requirementsHelpers->checkMandatoryRequirements();
        [$requiredFilesAndDirs, $checkAgain] = $this->requirementsHelpers->checkFolderAndFilePermissions();

        return [
            'requirements' => $requirements,
            'files_dirs' => $requiredFilesAndDirs,
            'php_settings' => $this->requirementsHelpers->checkOptionalRequirements(),
            'stop_install' => $stopInstall,
            'check_again' => $checkAgain,
        ];
    }
}
