<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

/**
 * Class Logout
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Index
 */
class Logout extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Users\Model\AuthenticationModel
     */
    protected $authenticationModel;

    /**
     * Login constructor.
     * @param Core\Controller\Context\FrontendContext $context
     * @param Users\Model\AuthenticationModel $authenticationModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Users\Model\AuthenticationModel $authenticationModel)
    {
        parent::__construct($context);

        $this->authenticationModel = $authenticationModel;
    }

    /**
     * @param string $last
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($last = '')
    {
        $this->authenticationModel->logout();

        if (!empty($last)) {
            $lastPage = base64_decode($last);

            if (!preg_match('/^((acp|users)\/)/', $lastPage)) {
                return $this->redirect()->temporary($lastPage);
            }
        }
        return $this->redirect()->toNewPage($this->appPath->getWebRoot());
    }
}
