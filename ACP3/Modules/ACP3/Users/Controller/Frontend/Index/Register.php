<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users;

class Register extends Core\Controller\AbstractFormAction
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
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\UserRepository
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
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Date $date,
        Core\Helpers\Alerts $alertsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Users\Model\Repository\UserRepository $userRepository,
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
        $this->alertsHelper = $alertsHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Users\Installer\Schema::MODULE_NAME);

        if ($this->user->isAuthenticated() === true) {
            return $this->redirect()->toNewPage($this->appPath->getWebRoot());
        } elseif ($settings['enable_registration'] == 0) {
            $this->setContent(
                $this->alertsHelper->errorBox(
                $this->translator->t('users', 'user_registration_disabled')
            )
            );
        }

        $defaults = [
            'nickname' => '',
            'mail' => '',
        ];

        return [
            'form' => \array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->registrationFormValidation->validate($formData);

                $mailIsSent = $this->sendRegistrationEmail($formData);

                $salt = $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);
                $insertValues = [
                    'id' => '',
                    'nickname' => $this->secureHelper->strEncode($formData['nickname']),
                    'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd'], 'sha512'),
                    'pwd_salt' => $salt,
                    'mail' => $formData['mail'],
                    'registration_date' => $this->date->getCurrentDateTime(),
                ];

                $lastId = $this->userRepository->insert($insertValues);
                $bool2 = $this->permissionsHelpers->updateUserRoles([2], $lastId);

                $this->setTemplate($this->alertsHelper->confirmBox(
                    $this->translator->t(
                        'users',
                        $mailIsSent === true && $lastId !== false && $bool2 !== false ? 'register_success' : 'register_error'
                    ),
                    $this->appPath->getWebRoot()
                ));
            },
            $this->request->getFullPath()
        );
    }

    /**
     * @param array $formData
     *
     * @return bool
     */
    protected function sendRegistrationEmail(array $formData)
    {
        $systemSettings = $this->config->getSettings(Schema::MODULE_NAME);
        $settings = $this->config->getSettings(Users\Installer\Schema::MODULE_NAME);

        $subject = $this->translator->t(
            'users',
            'register_mail_subject',
            [
                '{title}' => $systemSettings['site_title'],
                '{host}' => $this->request->getHost(),
            ]
        );
        $body = $this->translator->t(
            'users',
            'register_mail_message',
            [
                '{name}' => $formData['nickname'],
                '{mail}' => $formData['mail'],
                '{password}' => $formData['pwd'],
                '{title}' => $systemSettings['site_title'],
                '{host}' => $this->request->getHost(),
            ]
        );

        $data = (new Core\Mailer\MailerMessage())
            ->setRecipients($formData['mail'])
            ->setFrom($settings['mail'])
            ->setSubject($subject)
            ->setBody($body)
            ->setTemplate('Users/layout.email.register.tpl');

        return $this->sendEmail->execute($data);
    }
}
