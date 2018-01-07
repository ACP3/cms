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
    protected $accountFormValidation;
    /**
     * @var Users\Model\UsersModel
     */
    protected $usersModel;
    /**
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext             $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface         $block
     * @param Users\Model\UsersModel                                    $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validation\AccountFormValidation $accountFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AccountFormValidation $accountFormValidation
    ) {
        parent::__construct($context);

        $this->accountFormValidation = $accountFormValidation;
        $this->usersModel = $usersModel;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setDataById($this->user->getUserId())
            ->setRequestData($this->request->getPost()->all())
            ->render();
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
