<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;


use ACP3\Core\Controller\AbstractWidgetAction;

/**
 * Class Hash
 * @package ACP3\Modules\ACP3\Users\Controller\Widget\Index
 */
class Hash extends AbstractWidgetAction
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute()
    {
        $this->response->setVary('Cookie');
        $this->response->setPublic();
        $this->response->setMaxAge(60);
        $this->response->headers->add([
            'Content-type' => 'application/vnd.fos.user-context-hash',
            'X-User-Context-Hash' => $this->generateUserContextHash()
        ]);

        return $this->response;
    }

    /**
     * @return string
     */
    private function generateUserContextHash()
    {
        $settings = $this->config->getSettings('system');
        $hash = $settings['security_secret'];

        if ($this->user->isAuthenticated()) {
            $hash .= implode('-', $this->acl->getUserRoleIds($this->user->getUserId()));

            if (intval($settings['cache_vary_user']) === 1) {
                $hash .= '-' . $this->user->getUserId();
            }
        }

        return hash('sha512', $hash);
    }
}
