<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Users;

class EditPost extends AbstractAction
{
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        UserModelInterface $user,
        private Users\Model\UsersModel $usersModel,
        private Users\Validation\AccountFormValidation $accountFormValidation
    ) {
        parent::__construct($context, $user);
        $this->user = $user;
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->accountFormValidation
                    ->setUserId($this->user->getUserId())
                    ->validate($formData);

                $result = $this->usersModel->save($formData, $this->user->getUserId());

                return $this->actionHelper->setRedirectMessage(
                    $result,
                    $this->translator->t('system', $result ? 'edit_success' : 'edit_error')
                );
            }
        );
    }
}
