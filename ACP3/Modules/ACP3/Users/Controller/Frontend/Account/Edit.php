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
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Helpers\Forms
     */
    protected $userFormsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AccountFormValidation
     */
    protected $accountFormValidation;
    /**
     * @var Users\Model\UsersModel
     */
    protected $usersModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext             $context
     * @param \ACP3\Core\Helpers\FormToken                              $formTokenHelper
     * @param \ACP3\Modules\ACP3\Users\Helpers\Forms                    $userFormsHelper
     * @param \ACP3\Modules\ACP3\Users\Validation\AccountFormValidation $accountFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Users\Helpers\Forms $userFormsHelper,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AccountFormValidation $accountFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->userFormsHelper = $userFormsHelper;
        $this->accountFormValidation = $accountFormValidation;
        $this->usersModel = $usersModel;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $user = $this->user->getUserInfo();

        $this->view->assign(
            $this->userFormsHelper->fetchUserProfileFormFields(
                $user['birthday'],
                $user['country'],
                $user['gender']
            )
        );

        return [
            'contact' => $this->userFormsHelper->fetchContactDetails(
                $user['mail'],
                $user['website'],
                $user['icq'],
                $user['skype']
            ),
            'form' => \array_merge($user, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
