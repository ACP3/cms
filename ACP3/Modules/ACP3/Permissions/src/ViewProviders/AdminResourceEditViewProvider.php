<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\ViewProviders;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository;

class AdminResourceEditViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\PrivilegeRepository
     */
    private $privilegeRepository;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        Modules $modules,
        PrivilegeRepository $privilegeRepository,
        RequestInterface $request
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->modules = $modules;
        $this->privilegeRepository = $privilegeRepository;
        $this->request = $request;
    }

    /**
     * @throws \ReflectionException
     */
    public function __invoke(array $resource): array
    {
        $defaults = [
            'resource' => $resource['page'],
            'area' => $resource['area'],
            'controller' => $resource['controller'],
        ];

        return [
            'modules' => $this->fetchActiveModules($resource['module_name']),
            'areas' => $this->fetchAreas($resource['area']),
            'privileges' => $this->fetchPrivileges($resource['privilege_id']),
            'form' => \array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    private function fetchActiveModules(?string $currentModule = null): array
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
     * @throws \ReflectionException
     */
    private function fetchAreas(?string $currentArea = null): array
    {
        $areas = \array_values(AreaEnum::getAreas());

        return $this->formsHelper->choicesGenerator('area', \array_combine($areas, $areas), $currentArea);
    }

    private function fetchPrivileges(int $privilegeId): array
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
}
