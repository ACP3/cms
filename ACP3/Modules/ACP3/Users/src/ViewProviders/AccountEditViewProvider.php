<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Users\Helpers\Forms as UserFormsHelper;

class AccountEditViewProvider
{
    public function __construct(private FormToken $formTokenHelper, private RequestInterface $request, private UserModelInterface $userModel, private UserFormsHelper $userFormsHelper)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $user = $this->userModel->getUserInfo();

        return array_merge(
            $this->userFormsHelper->fetchUserProfileFormFields(
                $user['birthday'],
                $user['country'],
                $user['gender']
            ),
            [
                'contact' => $this->userFormsHelper->fetchContactDetails(
                    $user['mail'],
                    $user['website'],
                    $user['icq'],
                    $user['skype']
                ),
                'form' => array_merge($user, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ]
        );
    }
}
