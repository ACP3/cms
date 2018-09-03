<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Event\Listener;

use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;

class OnCaptchaAdminSettingsCustomFieldsListener
{
    /**
     * @var View
     */
    private $view;

    /**
     * OnCaptchaAdminSettingsCustomFieldsListener constructor.
     *
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @param TemplateEvent $event
     */
    public function __invoke(TemplateEvent $event)
    {
        $params = $event->getParameters();
        $this->view->assign('form', $params['form']);

        $this->view->displayTemplate('Captcha/Partials/captcha_recaptcha.admin_settings.tpl');
    }
}
