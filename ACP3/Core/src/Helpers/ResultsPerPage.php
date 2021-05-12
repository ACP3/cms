<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class ResultsPerPage
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var array
     */
    private $resultsPerPage = [];

    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    public function getResultsPerPage(string $moduleName): int
    {
        if (!isset($this->resultsPerPage[$moduleName])) {
            $moduleSettings = $this->settings->getSettings($moduleName);

            if (!empty($moduleSettings['entries'])) {
                $this->resultsPerPage[$moduleName] = $moduleSettings['entries'];
            } else {
                $moduleName = Schema::MODULE_NAME;

                $this->getResultsPerPage($moduleName);
            }
        }

        return (int) $this->resultsPerPage[$moduleName];
    }
}
