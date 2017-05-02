<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users;

class Register extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
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
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;
    /**
     * @var Users\Model\UsersModel
     */
    private $usersModel;

    /**
     * Register constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param Users\Model\UsersModel $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validation\RegistrationFormValidation $registrationFormValidation
     * @param \ACP3\Modules\ACP3\Permissions\Helpers $permissionsHelpers
     * @param \ACP3\Core\Helpers\SendEmail $sendEmail
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Core\Helpers\Secure $secureHelper,
        Users\Model\UsersModel $usersModel,
        Users\Validation\RegistrationFormValidation $registrationFormValidation,
        Permissions\Helpers $permissionsHelpers,
        Core\Helpers\SendEmail $sendEmail
    ) {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->registrationFormValidation = $registrationFormValidation;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->sendEmail = $sendEmail;
        $this->block = $block;
        $this->usersModel = $usersModel;
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
            $this->setContent($this->get('core.helpers.alerts')->errorBox(
                $this->translator->t('users', 'user_registration_disabled'))
            );
        }

        return $this->block
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

                $this->registrationFormValidation->validate($formData);

                $mailIsSent = $this->sendRegistrationEmail($formData);

                $salt = $this->secureHelper->salt(Users\Model\UserModel::SALT_LENGTH);
                $insertValues = [
                    'nickname' => $formData['nickname'],
                    'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd'], 'sha512'),
                    'pwd_salt' => $salt,
                    'mail' => $formData['mail'],
                    'registration_date' => 'now',
                ];

                $lastId = $this->usersModel->save($insertValues);
                $bool2 = $this->permissionsHelpers->updateUserRoles([2], $lastId);

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox(
                    $this->translator->t('users',
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
            ]);
        $body = $this->translator->t(
            'users',
            'register_mail_message',
            [
                '{name}' => $formData['nickname'],
                '{mail}' => $formData['mail'],
                '{password}' => $formData['pwd'],
                '{title}' => $systemSettings['site_title'],
                '{host}' => $this->request->getHost()
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
