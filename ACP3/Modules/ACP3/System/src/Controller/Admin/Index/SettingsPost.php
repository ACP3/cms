<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\System;

class SettingsPost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\System\Validation\AdminSettingsFormValidation
     */
    private $systemValidator;
    /**
     * @var Core\Helpers\Secure
     */
    private $secure;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Core\Helpers\Secure $secure,
        System\Validation\AdminSettingsFormValidation $systemValidator
    ) {
        parent::__construct($context);

        $this->systemValidator = $systemValidator;
        $this->secure = $secure;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke()
    {
        return $this->actionHelper->handleSettingsPostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->systemValidator->validate($formData);

                $data = [
                    'cache_images' => (int) $formData['cache_images'],
                    'cache_lifetime' => (int) $formData['cache_lifetime'],
                    'date_format_long' => $this->secure->strEncode($formData['date_format_long']),
                    'date_format_short' => $this->secure->strEncode($formData['date_format_short']),
                    'date_time_zone' => $formData['date_time_zone'],
                    'entries' => (int) $formData['entries'],
                    'flood' => (int) $formData['flood'],
                    'homepage' => $formData['homepage'],
                    'lang' => $formData['language'],
                    'mod_rewrite' => (int) $formData['mod_rewrite'],
                    'mailer_smtp_auth' => (int) $formData['mailer_smtp_auth'],
                    'mailer_smtp_host' => $formData['mailer_smtp_host'],
                    'mailer_smtp_password' => $formData['mailer_smtp_password'],
                    'mailer_smtp_port' => (int) $formData['mailer_smtp_port'],
                    'mailer_smtp_security' => $formData['mailer_smtp_security'],
                    'mailer_smtp_user' => $formData['mailer_smtp_user'],
                    'mailer_type' => $formData['mailer_type'],
                    'maintenance_message' => $formData['maintenance_message'],
                    'maintenance_mode' => (int) $formData['maintenance_mode'],
                    'page_cache_is_enabled' => (int) $formData['page_cache_is_enabled'],
                    'page_cache_purge_mode' => (int) $formData['page_cache_purge_mode'],
                    'site_title' => $this->secure->strEncode($formData['site_title']),
                    'site_subtitle' => $this->secure->strEncode($formData['site_subtitle']),
                    'site_subtitle_homepage_mode' => (int) $formData['site_subtitle_homepage_mode'],
                    'site_subtitle_mode' => (int) $formData['site_subtitle_mode'],
                    'wysiwyg' => $formData['wysiwyg'],
                ];

                return $this->config->saveSettings($data, System\Installer\Schema::MODULE_NAME);
            }
        );
    }
}
