<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;


use ACP3\Core\Controller\WidgetAction;

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
        $this->response->headers->set('Content-type', 'application/vnd.fos.user-context-hash');
        $this->response->headers->set('X-User-Context-Hash', $this->generateUserContextHash());

        return $this->response;
    }

    /**
     * @return string
     */
    private function generateUserContextHash()
    {
        $userRoles = $this->acl->getUserRoleIds($this->user->getUserId());

        return hash('sha512', session_id() . '-' . $this->user->getUserId() . '-' . serialize($userRoles));
    }
}
