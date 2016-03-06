<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Register
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Index
 */
class Register extends Core\Controller\FrontendAction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\RegistrationFormValidation
     */
    protected $registrationFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;
    /**
     * @var \ACP3\Core\Helpers\SendEmail
     */
    protected $sendEmail;

    /**
     * Register constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                  $context
     * @param \ACP3\Core\Date                                                $date
     * @param \ACP3\Core\Helpers\FormToken                                   $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                                      $secureHelper
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository                  $userRepository
     * @param \ACP3\Modules\ACP3\Users\Validation\RegistrationFormValidation $registrationFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers                         $permissionsHelpers
     * @param \ACP3\Core\Helpers\SendEmail                                   $sendEmail
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Users\Model\UserRepository $userRepository,
        Users\Validation\RegistrationFormValidation $registrationFormValidation,
        Permissions\Helpers $permissionsHelpers,
        Core\Helpers\SendEmail $sendEmail
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->userRepository = $userRepository;
        $this->registrationFormValidation = $registrationFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->sendEmail = $sendEmail;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings('users');

        if ($this->user->isAuthenticated() === true) {
            return $this->redirect()->toNewPage($this->appPath->getWebRoot());
        } elseif ($settings['enable_registration'] == 0) {
            $this->setContent($this->get('core.helpers.alerts')->errorBox(
                $this->translator->t('users', 'user_registration_disabled'))
            );
        } else {
            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $settings);
            }

            $defaults = [
                'nickname' => '',
                'mail' => '',
            ];

            return [
                'form' => array_merge($defaults, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }
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
                $this->registrationFormValidation->validate($formData);

                $systemSettings = $this->config->getSettings('system');
                $seoSettings = $this->config->getSettings('seo');

                $subject = $this->translator->t(
                    'users',
                    'register_mail_subject',
                    [
                        '{title}' => $seoSettings['title'],
                        '{host}' => $this->request->getHostname(),
                    ]);
                $body = $this->translator->t(
                    'users',
                    'register_mail_message',
                    [
                        '{name}' => $formData['nickname'],
                        '{mail}' => $formData['mail'],
                        '{password}' => $formData['pwd'],
                        '{title}' => $seoSettings['title'],
                        '{host}' => $this->request->getHostname()
                    ]
                );
                $mailIsSent = $this->sendEmail->execute('', $formData['mail'], $settings['mail'], $subject, $body);

                $salt = $this->secureHelper->salt(Core\User::SALT_LENGTH);
                $insertValues = [
                    'id' => '',
                    'nickname' => $this->get('core.helpers.secure')->strEncode($formData['nickname']),
                    'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd'], 'sha512'),
                    'pwd_salt' => $salt,
                    'mail' => $formData['mail'],
                    'date_format_long' => $systemSettings['date_format_long'],
                    'date_format_short' => $systemSettings['date_format_short'],
                    'time_zone' => $systemSettings['date_time_zone'],
                    'language' => $systemSettings['lang'],
                    'entries' => $systemSettings['entries'],
                    'registration_date' => $this->date->getCurrentDateTime(),
                ];

                $lastId = $this->userRepository->insert($insertValues);
                $bool2 = $this->permissionsHelpers->updateUserRoles([2], $lastId);

                $this->formTokenHelper->unsetFormToken();

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox(
                    $this->translator->t('users',
                        $mailIsSent === true && $lastId !== false && $bool2 !== false ? 'register_success' : 'register_error'),
                    $this->appPath->getWebRoot()
                ));
            },
            $this->request->getFullPath()
        );
    }
}
