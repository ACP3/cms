<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\ViewProviders;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;

class GuestbookCreateViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        FormToken $formTokenHelper,
        RequestInterface $request,
        UserModelInterface $user
    ) {
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->user = $user;
    }

    public function __invoke(): array
    {
        return [
            'form' => array_merge($this->fetchFormDefaults(), $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    private function fetchFormDefaults(): array
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'mail' => '',
            'mail_disabled' => false,
            'website' => '',
            'website_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $users = $this->user->getUserInfo();
            $defaults['name'] = $users['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['mail'] = $users['mail'];
            $defaults['mail_disabled'] = true;
            $defaults['website'] = $users['website'];
            $defaults['website_disabled'] = !empty($users['website']);
        }

        return $defaults;
    }
}
