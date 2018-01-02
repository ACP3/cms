<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;
    /**
     * @var Users\Model\UsersModel
     */
    protected $usersModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param Users\Model\UsersModel $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers $permissionsHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Core\Helpers\Secure $secureHelper,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->usersModel = $usersModel;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminFormValidation->validate($formData);

            $salt = $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);

            $formData = array_merge($formData, [
                'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd'], 'sha512'),
                'pwd_salt' => $salt,
                'registration_date' => 'now',
            ]);

            $lastId = $this->usersModel->save($formData);

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $lastId);

            return $lastId;
        });
    }
}
