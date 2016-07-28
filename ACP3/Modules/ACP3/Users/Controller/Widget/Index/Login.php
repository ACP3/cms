<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Users\Installer\Schema;

/**
 * Class Login
 * @package ACP3\Modules\ACP3\Users\Controller\Widget\Index
 */
class Login extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * Displays the login mask, if the user is not already logged in
     *
     * @return array|void
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings('system')['cache_lifetime']);
        if ($this->user->isAuthenticated() === false) {
            $prefix = $this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN ? 'acp/' : '';
            $currentPage = base64_encode($prefix . $this->request->getQuery());
            $settings = $this->config->getSettings(Schema::MODULE_NAME);

            return [
                'enable_registration' => $settings['enable_registration'],
                'redirect_uri' => $this->request->getPost()->get('redirect_uri', $currentPage)
            ];
        }

        $this->setContent(false);
    }
}
