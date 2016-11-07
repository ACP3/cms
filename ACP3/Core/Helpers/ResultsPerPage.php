<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

/**
 * Created by PhpStorm.
 * User: tinog
 * Date: 07.11.2016
 * Time: 01:28
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

    /**
     * ResultsPerPage constructor.
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $moduleName
     * @return int
     */
    public function getResultsPerPage($moduleName)
    {
        if (!isset($this->resultsPerPage[$moduleName])) {
            $moduleName = $this->addResultsToCache($moduleName);
        }

        return (int)$this->resultsPerPage[$moduleName];
    }

    /**
     * @param string $moduleName
     * @return string
     */
    protected function addResultsToCache($moduleName)
    {
        $moduleSettings = $this->settings->getSettings($moduleName);

        if (!empty($moduleName['entries'])) {
            $this->resultsPerPage[$moduleName] = $moduleSettings['entries'];
        } else {
            $moduleName = Schema::MODULE_NAME;

            $this->getResultsPerPage($moduleName);
        }

        return $moduleName;
    }
}
