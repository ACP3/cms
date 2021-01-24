<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\ViewProviders;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;

class AdminModulesViewProvider
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(Modules $modules, Translator $translator)
    {
        $this->modules = $modules;
        $this->translator = $translator;
    }

    public function __invoke(): array
    {
        $installedModules = $newModules = [];

        foreach ($this->modules->getAllModulesAlphabeticallySorted() as $moduleName => $values) {
            $translatedModuleName = $this->translator->t($moduleName, $moduleName);
            if ($values['installable'] === false || $this->modules->isInstalled($values['name']) === true) {
                $installedModules[$translatedModuleName] = $values;
            } else {
                $newModules[$translatedModuleName] = $values;
            }
        }

        \ksort($installedModules);
        \ksort($newModules);

        return [
            'installed_modules' => $installedModules,
            'new_modules' => $newModules,
        ];
    }
}
