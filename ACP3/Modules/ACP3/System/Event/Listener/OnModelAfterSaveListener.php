<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class OnModelAfterSaveListener
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * OnModelAfterSaveListener constructor.
     * @param SettingsInterface $settings
     */
    public function __construct(
        SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    public function invalidatePageCache()
    {
        $this->settings->saveSettings(['page_cache_is_valid' => false], Schema::MODULE_NAME);
    }
}
