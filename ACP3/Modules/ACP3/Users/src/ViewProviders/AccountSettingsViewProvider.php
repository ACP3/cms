<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Users\Helpers\Forms as UserFormsHelper;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class AccountSettingsViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    private $userModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\Helpers\Forms
     */
    private $userFormsHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(
        FormToken $formTokenHelper,
        RequestInterface $request,
        UserModel $userModel,
        UserFormsHelper $userFormsHelper)
    {
        $this->formTokenHelper = $formTokenHelper;
        $this->userModel = $userModel;
        $this->userFormsHelper = $userFormsHelper;
        $this->request = $request;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $user = $this->userModel->getUserInfo();

        return \array_merge(
            $this->userFormsHelper->fetchUserSettingsFormFields(
                $user['address_display'],
                $user['birthday_display'],
                $user['country_display'],
                $user['mail_display']
            ),
            [
                'form' => \array_merge($user, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ]
        );
    }
}
