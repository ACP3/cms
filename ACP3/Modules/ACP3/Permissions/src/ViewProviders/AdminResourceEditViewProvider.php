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

class AdminResourceEditViewProvider
{
    public function __construct(private Forms $formsHelper, private FormToken $formTokenHelper, private Modules $modules, private RequestInterface $request, private Translator $translator)
    {
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
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    private function fetchActiveModules(?string $currentModule = null): array
    {
        $modules = [];
        foreach ($this->modules->getInstalledModules() as $info) {
            $modules[$info['name']] = $this->translator->t($info['name'], $info['name']);
        }

        uasort($modules, static fn ($a, $b) => $a <=> $b);

        return $this->formsHelper->choicesGenerator('modules', $modules, $currentModule);
    }

    /**
     * @throws \ReflectionException
     */
    private function fetchAreas(?string $currentArea = null): array
    {
        $areas = array_values(AreaEnum::getAreas());

        return $this->formsHelper->choicesGenerator('area', array_combine($areas, $areas), $currentArea);
    }
}
