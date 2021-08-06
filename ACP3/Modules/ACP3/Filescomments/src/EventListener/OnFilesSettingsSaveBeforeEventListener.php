<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\EventListener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Core\Settings\Event\SettingsSaveEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Filescomments\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnFilesSettingsSaveBeforeEventListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, RequestInterface $request, SettingsInterface $settings)
    {
        $this->request = $request;
        $this->settings = $settings;
        $this->modules = $modules;
    }

    public function __invoke(SettingsSaveEvent $event): void
    {
        if (!$this->modules->isInstalled(CommentsSchema::MODULE_NAME) || !$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $this->settings->saveSettings(
            ['comments' => (int) $this->request->getPost()->get('comments')],
            Schema::MODULE_NAME
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'files.settings.save_before' => '__invoke',
        ];
    }
}
