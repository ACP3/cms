<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Users\Installer\Schema;

class Login extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * Displays the login mask, if the user is not already logged in.
     */
    public function execute(): ?array
    {
        $this->setCacheResponseCacheable();

        if ($this->user->isAuthenticated() === false) {
            $prefix = $this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN ? 'acp/' : '';
            $currentPage = \base64_encode($prefix . $this->request->getQuery());
            $settings = $this->config->getSettings(Schema::MODULE_NAME);

            return [
                'enable_registration' => $settings['enable_registration'],
                'redirect_uri' => $this->request->getPost()->get('redirect_uri', $currentPage),
            ];
        }

        $this->setContent(false);

        return null;
    }
}