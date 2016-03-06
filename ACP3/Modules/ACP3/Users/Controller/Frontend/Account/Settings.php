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
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AccountSettingsFormValidation
     */
    protected $accountSettingsFormValidation;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                     $context
     * @param \ACP3\Core\Helpers\FormToken                                      $formTokenHelper
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository                     $userRepository
     * @param \ACP3\Modules\ACP3\Users\Validation\AccountSettingsFormValidation $accountSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Users\Model\UserRepository $userRepository,
        Users\Validation\AccountSettingsFormValidation $accountSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->userRepository = $userRepository;
        $this->accountSettingsFormValidation = $accountSettingsFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings('users');

        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all(), $settings);
        }

        $user = $this->userRepository->getOneById($this->user->getUserId());

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

                $updateValues = [
                    'mail_display' => (int)$formData['mail_display'],
                    'birthday_display' => (int)$formData['birthday_display'],
                    'address_display' => (int)$formData['address_display'],
                    'country_display' => (int)$formData['country_display'],
                    'date_format_long' => $this->get('core.helpers.secure')->strEncode($formData['date_format_long']),
                    'date_format_short' => $this->get('core.helpers.secure')->strEncode($formData['date_format_short']),
                    'time_zone' => $formData['date_time_zone'],
                ];
                if ($settings['language_override'] == 1) {
                    $updateValues['language'] = $formData['language'];
                }
                if ($settings['entries_override'] == 1) {
                    $updateValues['entries'] = (int)$formData['entries'];
                }

                $bool = $this->userRepository->update($updateValues, $this->user->getUserId());

                $this->formTokenHelper->unsetFormToken();

                return $this->redirectMessages()->setMessage($bool,
                    $this->translator->t('system', $bool !== false ? 'settings_success' : 'settings_error'));
            }
        );
    }
}
