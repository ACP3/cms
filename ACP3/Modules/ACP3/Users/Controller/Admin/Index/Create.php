<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
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
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext              $context
     * @param \ACP3\Core\Date                                         $date
     * @param \ACP3\Core\Helpers\FormToken                            $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                               $secureHelper
     * @param \ACP3\Core\Helpers\Forms                                $formsHelpers
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository           $userRepository
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers                  $permissionsHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Model\UserRepository $userRepository,
        Users\Validation\AdminFormValidation $adminFormValidation,
        Permissions\Helpers $permissionsHelpers)
    {
        parent::__construct($context, $formsHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->userRepository = $userRepository;
        $this->adminFormValidation = $adminFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        $systemSettings = $this->config->getSettings('system');

        $this->view->assign(
            $this->get('users.helpers.forms')->fetchUserSettingsFormFields(
                (int)$systemSettings['entries'],
                $systemSettings['lang'],
                $systemSettings['date_time_zone']
            )
        );
        $this->view->assign($this->get('users.helpers.forms')->fetchUserProfileFormFields());

        $defaults = [
            'nickname' => '',
            'realname' => '',
            'mail' => '',
            'website' => '',
            'street' => '',
            'house_number' => '',
            'zip' => '',
            'city' => '',
            'date_format_long' => $systemSettings['date_format_long'],
            'date_format_short' => $systemSettings['date_format_short']
        ];

        return [
            'roles' => $this->fetchUserRoles(),
            'super_user' => $this->fetchIsSuperUser(),
            'contact' => $this->get('users.helpers.forms')->fetchContactDetails(),
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->adminFormValidation->validate($formData);

            $salt = $this->secureHelper->salt(15);

            $insertValues = [
                'id' => '',
                'super_user' => (int)$formData['super_user'],
                'nickname' => $this->get('core.helpers.secure')->strEncode($formData['nickname']),
                'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd'], 'sha512'),
                'pwd_salt' => $salt,
                'realname' => $this->get('core.helpers.secure')->strEncode($formData['realname']),
                'gender' => (int)$formData['gender'],
                'birthday' => $formData['birthday'],
                'birthday_display' => (int)$formData['birthday_display'],
                'mail' => $formData['mail'],
                'mail_display' => isset($formData['mail_display']) ? 1 : 0,
                'website' => $this->get('core.helpers.secure')->strEncode($formData['website']),
                'icq' => $formData['icq'],
                'skype' => $this->get('core.helpers.secure')->strEncode($formData['skype']),
                'street' => $this->get('core.helpers.secure')->strEncode($formData['street']),
                'house_number' => $this->get('core.helpers.secure')->strEncode($formData['house_number']),
                'zip' => $this->get('core.helpers.secure')->strEncode($formData['zip']),
                'city' => $this->get('core.helpers.secure')->strEncode($formData['city']),
                'address_display' => isset($formData['address_display']) ? 1 : 0,
                'country' => $this->get('core.helpers.secure')->strEncode($formData['country']),
                'country_display' => isset($formData['country_display']) ? 1 : 0,
                'date_format_long' => $this->get('core.helpers.secure')->strEncode($formData['date_format_long']),
                'date_format_short' => $this->get('core.helpers.secure')->strEncode($formData['date_format_short']),
                'time_zone' => $formData['date_time_zone'],
                'language' => $formData['language'],
                'entries' => (int)$formData['entries'],
                'draft' => '',
                'registration_date' => $this->date->getCurrentDateTime(),
            ];

            $lastId = $this->userRepository->insert($insertValues);

            $this->permissionsHelpers->updateUserRoles($formData['roles'], $lastId);

            $this->formTokenHelper->unsetFormToken();

            return $lastId;
        });
    }
}
