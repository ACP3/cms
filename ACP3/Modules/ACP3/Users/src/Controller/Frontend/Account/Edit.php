<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

class Edit extends AbstractAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AccountFormValidation
     */
    private $accountFormValidation;
    /**
     * @var Users\Model\UsersModel
     */
    private $usersModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\AccountEditViewProvider
     */
    private $accountEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Users\ViewProviders\AccountEditViewProvider $accountEditViewProvider,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AccountFormValidation $accountFormValidation
    ) {
        parent::__construct($context);

        $this->accountFormValidation = $accountFormValidation;
        $this->usersModel = $usersModel;
        $this->accountEditViewProvider = $accountEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(): array
    {
        return ($this->accountEditViewProvider)();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->accountFormValidation
                    ->setUserId($this->user->getUserId())
                    ->validate($formData);

                $bool = $this->usersModel->save($formData, $this->user->getUserId());

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $this->translator->t('system', $bool !== false ? 'edit_success' : 'edit_error')
                );
            }
        );
    }
}
