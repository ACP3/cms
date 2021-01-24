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
use ACP3\Core\I18n\Translator;
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
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        Modules $modules,
        PrivilegeRepository $privilegeRepository,
        RequestInterface $request,
        Translator $translator
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->modules = $modules;
        $this->privilegeRepository = $privilegeRepository;
        $this->request = $request;
        $this->translator = $translator;
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
        $modules = [];
        foreach ($this->modules->getActiveModules() as $info) {
            $modules[$info['name']] = $this->translator->t($info['name'], $info['name']);
        }

        \uasort($modules, static function ($a, $b) {
            return $a <=> $b;
        });

        return $this->formsHelper->choicesGenerator('modules', $modules, $currentModule);
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
        $privileges = [];
        foreach ($this->privilegeRepository->getAllPrivileges() as $i => $privilege) {
            $privileges[(int) $privilege['id']] = $privilege['key'];
        }

        return $this->formsHelper->choicesGenerator('privileges', $privileges, $privilegeId);
    }
}
