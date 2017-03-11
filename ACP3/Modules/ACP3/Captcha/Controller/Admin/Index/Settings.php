<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Controller\Admin\Index;


use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;

class Settings extends AbstractAdminAction
{
    /**
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        return [
            'form' => array_merge($settings, $this->request->getPost()->all())
        ];
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            return $this->config->saveSettings($formData, Schema::MODULE_NAME);
        });
    }
}
