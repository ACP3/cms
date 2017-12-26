<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Users\Controller\Admin\Index
 */
class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
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
     * @var Users\Helpers\Forms
     */
    private $userFormsHelpers;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param \ACP3\Core\Helpers\Forms $formsHelpers
     * @param Users\Helpers\Forms $userFormsHelpers
     * @param Users\Model\UsersModel $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers $permissionsHelpers
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Helpers\Forms $userFormsHelpers,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers
    ) {
        parent::__construct($context, $formsHelpers);

        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->usersModel = $usersModel;
        $this->userFormsHelpers = $userFormsHelpers;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $this->view->assign($this->userFormsHelpers->fetchUserSettingsFormFields());
        $this->view->assign($this->userFormsHelpers->fetchUserProfileFormFields());

        $defaults = [
            'nickname' => '',
            'realname' => '',
            'mail' => '',
            'website' => '',
            'street' => '',
            'house_number' => '',
            'zip' => '',
            'city' => '',
        ];

        return [
            'roles' => $this->fetchUserRoles(),
            'super_user' => $this->fetchIsSuperUser(),
            'contact' => $this->userFormsHelpers->fetchContactDetails(),
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
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
