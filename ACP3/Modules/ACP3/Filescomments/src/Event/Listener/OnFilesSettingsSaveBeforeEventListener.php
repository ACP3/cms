<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\Event\Listener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\Event\SettingsSaveEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Filescomments\Installer\Schema;

class OnFilesSettingsSaveBeforeEventListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(RequestInterface $request, SettingsInterface $settings)
    {
        $this->request = $request;
        $this->settings = $settings;
    }

    public function __invoke(SettingsSaveEvent $event): void
    {
        $this->settings->saveSettings(
            ['comments' => (int) $this->request->getPost()->get('comments')],
            Schema::MODULE_NAME
        );
    }
}
