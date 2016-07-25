<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;


use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Core\Controller\Context\AdminContext;
use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository;

class AbstractFormAction extends AbstractAdminAction
{
    /**
     * @var Forms
     */
    protected $formsHelper;
    /**
     * @var PrivilegeRepository
     */
    protected $privilegeRepository;

    public function __construct(
        AdminContext $context,
        Forms $formsHelper,
        PrivilegeRepository $privilegeRepository
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->privilegeRepository = $privilegeRepository;
    }

    /**
     * @param int $privilegeId
     * @return array
     */
    protected function fetchPrivileges($privilegeId)
    {
        $privileges = $this->privilegeRepository->getAllPrivileges();
        $cPrivileges = count($privileges);
        for ($i = 0; $i < $cPrivileges; ++$i) {
            $privileges[$i]['selected'] = $this->formsHelper->selectEntry(
                'privileges',
                $privileges[$i]['id'],
                $privilegeId
            );
        }

        return $privileges;
    }

    /**
     * @param string $currentModule
     * @return array
     */
    protected function fetchActiveModules($currentModule = '')
    {
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $row) {
            $modules[$row['name']]['selected'] = $this->formsHelper->selectEntry(
                'modules',
                $row['dir'],
                ucfirst(trim($currentModule))
            );
        }
        return $modules;
    }

    /**
     * @param string $moduleName
     * @return int
     */
    protected function fetchModuleId($moduleName)
    {
        $moduleInfo = $this->modules->getModuleInfo($moduleName);

        return isset($moduleInfo['id']) ? $moduleInfo['id'] : 0;
    }
}
