<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Settings;

interface SettingsInterface
{
    /**
     * Returns the module's settings from the cache.
     *
     * @return array<string, mixed>
     */
    public function getSettings(string $module): array;

    /**
     * Saves the module's settings to the database.
     *
     * @param array<string, mixed> $data
     */
    public function saveSettings(array $data, string $moduleName): bool;
}
