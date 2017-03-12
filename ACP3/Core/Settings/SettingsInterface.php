<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Settings;


interface SettingsInterface
{
    /**
     * Returns the module's settings from the cache
     *
     * @param string $module
     * @return array
     */
    public function getSettings($module);

    /**
     * Saves the module's settings to the database
     *
     * @param array $data
     * @param string $module
     *
     * @return bool
     */
    public function saveSettings(array $data, $module);
}
