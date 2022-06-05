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
    public function __construct(private readonly Modules $modules, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, array<string, mixed>>
     */
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

        ksort($installedModules);
        ksort($newModules);

        return [
            'installed_modules' => $installedModules,
            'new_modules' => $newModules,
        ];
    }
}
