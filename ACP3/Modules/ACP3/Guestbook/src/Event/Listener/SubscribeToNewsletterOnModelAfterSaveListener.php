<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Event\Listener;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Helper\Subscribe;

class SubscribeToNewsletterOnModelAfterSaveListener
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe|null
     */
    private $subscribe;

    /**
     * SubscribeToNewsletterOnModelAfterSaveListener constructor.
     */
    public function __construct(
        SettingsInterface $settings,
        RouterInterface $router,
        Translator $translator,
        ?Subscribe $subscribe = null
    ) {
        $this->settings = $settings;
        $this->router = $router;
        $this->translator = $translator;
        $this->subscribe = $subscribe;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        if ($this->settings->getSettings(Schema::MODULE_NAME)['newsletter_integration'] == 1 && $this->subscribe) {
            $formData = $event->getRawData();

            if (!empty($formData['subscribe_newsletter']) && !empty($formData['mail'])) {
                $this->subscribe->subscribeToNewsletter($formData['mail']);
            }
        }
    }
}
