<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Logout
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Index
 */
class Logout extends Core\Controller\FrontendController
{
    /**
     * @param string $last
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($last = '')
    {
        $this->user->logout();

        if (!empty($last)) {
            $lastPage = base64_decode($last);

            if (!preg_match('/^((acp|users)\/)/', $lastPage)) {
                return $this->redirect()->temporary($lastPage);
            }
        }
        return $this->redirect()->toNewPage($this->appPath->getWebRoot());
    }
}