<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\EventListener;

use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnCaptchaAdminSettingsCustomFieldsListener implements EventSubscriberInterface
{
    /**
     * @var View
     */
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function __invoke(TemplateEvent $event)
    {
        $params = $event->getParameters();
        $this->view->assign('form', $params['form']);

        $event->addContent($this->view->fetchTemplate('Captcha/Partials/captcha_recaptcha.admin_settings.tpl'));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'captcha.admin_settings.custom_fields' => '__invoke',
        ];
    }
}
