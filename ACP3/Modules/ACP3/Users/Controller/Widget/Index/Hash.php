<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;


use ACP3\Core\Application\Bootstrap\HttpCache;
use ACP3\Core\Controller\WidgetAction;
use ACP3\Core\Session\SessionHandlerInterface;

/**
 * Class Hash
 * @package ACP3\Modules\ACP3\Users\Controller\Widget\Index
 */
class Hash extends WidgetAction
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute()
    {
        $this->response->setPublic();
        $this->response->setVary('cookie');
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
        if ($this->user->isAuthenticated()) {
            $userRoles = implode('-', $this->acl->getUserRoleIds($this->user->getUserId()));

            return md5(
                $this->user->getUserId()
                . '-' . $userRoles
                . '-' . $this->request->getCookies()->get(SessionHandlerInterface::SESSION_NAME, '')
            );
        }

        return md5(HttpCache::USER_CONTEXT_GUEST);
    }
}
