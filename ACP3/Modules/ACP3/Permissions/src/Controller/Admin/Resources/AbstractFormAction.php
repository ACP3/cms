<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository;

class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var Forms
     */
    protected $formsHelper;
    /**
     * @var PrivilegeRepository
     */
    protected $privilegeRepository;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        FrontendContext $context,
        Modules $modules,
        Forms $formsHelper,
        PrivilegeRepository $privilegeRepository
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->privilegeRepository = $privilegeRepository;
        $this->modules = $modules;
    }

    /**
     * @param int $privilegeId
     *
     * @return array
     */
    protected function fetchPrivileges(int $privilegeId): array
    {
        $privileges = $this->privilegeRepository->getAllPrivileges();
        foreach ($privileges as $i => $privilege) {
            $privileges[$i]['selected'] = $this->formsHelper->selectEntry(
                'privileges',
                $privilege['id'],
                $privilegeId
            );
        }

        return $privileges;
    }

    /**
     * @param string $currentModule
     *
     * @return array
     */
    protected function fetchActiveModules(string $currentModule = ''): array
    {
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $row) {
            $modules[$row['name']]['selected'] = $this->formsHelper->selectEntry(
                'modules',
                $row['name'],
                \ucfirst(\trim($currentModule))
            );
        }

        return $modules;
    }

    /**
     * @param string $moduleName
     *
     * @return int
     */
    protected function fetchModuleId(string $moduleName): int
    {
        $moduleInfo = $this->modules->getModuleInfo($moduleName);

        return $moduleInfo['id'] ?? 0;
    }

    /**
     * @param string $currentArea
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function fetchAreas(string $currentArea = ''): array
    {
        $areas = \array_values(AreaEnum::getAreas());

        return $this->formsHelper->choicesGenerator('area', \array_combine($areas, $areas), $currentArea);
    }
}
