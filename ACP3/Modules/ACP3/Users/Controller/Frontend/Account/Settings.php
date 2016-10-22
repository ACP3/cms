<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Account
 */
class Settings extends AbstractAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AccountSettingsFormValidation
     */
    protected $accountSettingsFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Users\Helpers\Forms
     */
    protected $userFormsHelper;
    /**
     * @var Users\Model\UsersModel
     */
    protected $usersModel;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Users\Helpers\Forms $userFormsHelper
     * @param Users\Model\UsersModel $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validation\AccountSettingsFormValidation $accountSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Users\Helpers\Forms $userFormsHelper,
        Users\Model\UsersModel $usersModel,
        Users\Validation\AccountSettingsFormValidation $accountSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->userFormsHelper = $userFormsHelper;
        $this->accountSettingsFormValidation = $accountSettingsFormValidation;
        $this->usersModel = $usersModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Users\Installer\Schema::MODULE_NAME);

        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all(), $settings);
        }

        $user = $this->usersModel->getOneById($this->user->getUserId());

        $this->view->assign(
            $this->get('users.helpers.forms')->fetchUserSettingsFormFields(
                (int)$user['entries'],
                $user['language'],
                $user['time_zone'],
                $user['address_display'],
                $user['birthday_display'],
                $user['country_display'],
                $user['mail_display']
            )
        );

        return [
            'language_override' => $settings['language_override'],
            'entries_override' => $settings['entries_override'],
            'form' => array_merge($user, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData, $settings) {
                $this->accountSettingsFormValidation
                    ->setSettings($settings)
                    ->validate($formData);

                $formData['time_zone'] = $formData['date_time_zone'];

                if ($settings['language_override'] == 0) {
                    unset($formData['language']);
                }
                if ($settings['entries_override'] == 0) {
                    unset($formData['entries']);
                }

                $bool = $this->usersModel->save($formData, $this->user->getUserId());

                return $this->redirectMessages()->setMessage(
                    $bool,
                    $this->translator->t('system', $bool !== false ? 'settings_success' : 'settings_error')
                );
            }
        );
    }
}
