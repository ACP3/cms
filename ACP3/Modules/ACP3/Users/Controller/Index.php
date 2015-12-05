<?php

namespace ACP3\Modules\ACP3\Users\Controller;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Users\Controller
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
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
     * @var \ACP3\Modules\ACP3\Users\Validation\Register
     */
    protected $usersValidator;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Helpers
     */
    protected $permissionsHelpers;
    /**
     * @var \ACP3\Core\Helpers\SendEmail
     */
    protected $sendEmail;
    /**
     * @var \ACP3\Modules\ACP3\Captcha\Helpers
     */
    protected $captchaHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Date                               $date
     * @param \ACP3\Core\Pagination                         $pagination
     * @param \ACP3\Core\Helpers\FormToken                  $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                     $secureHelper
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     * @param \ACP3\Modules\ACP3\Users\Validation\Register  $usersValidator
     * @param \ACP3\Modules\ACP3\Permissions\Helpers        $permissionsHelpers
     * @param \ACP3\Core\Helpers\SendEmail                  $sendEmail
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Users\Model\UserRepository $userRepository,
        Users\Validation\Register $usersValidator,
        Permissions\Helpers $permissionsHelpers,
        Core\Helpers\SendEmail $sendEmail)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->userRepository = $userRepository;
        $this->usersValidator = $usersValidator;
        $this->permissionsHelpers = $permissionsHelpers;
        $this->sendEmail = $sendEmail;
    }

    /**
     * @param \ACP3\Modules\ACP3\Captcha\Helpers $captchaHelpers
     *
     * @return $this
     */
    public function setCaptchaHelpers(Captcha\Helpers $captchaHelpers)
    {
        $this->captchaHelpers = $captchaHelpers;

        return $this;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionForgotPwd()
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirect()->toNewPage(ROOT_DIR);
        }

        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_forgotPasswordPost($this->request->getPost()->all());
        }

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha());
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'form' => array_merge(['nick_mail' => ''], $this->request->getPost()->all())
        ];
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $users = $this->userRepository->getAll(POS, $this->user->getEntriesPerPage());
        $c_users = count($users);
        $allUsers = $this->userRepository->countAll();

        if ($c_users > 0) {
            $this->pagination->setTotalResults($allUsers);
            $this->pagination->display();

            $this->view->assign('users', $users);
        }

        return [
            'LANG_users_found' => sprintf($this->lang->t('users', 'users_found'), $allUsers)
        ];
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionLogin()
    {
        // Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
        if ($this->user->isAuthenticated() === true) {
            return $this->redirect()->toNewPage(ROOT_DIR);
        } elseif ($this->request->getPost()->isEmpty() === false) {
            $result = $this->user->login(
                Core\Functions::strEncode($this->request->getPost()->get('nickname', '')),
                $this->request->getPost()->get('pwd', ''),
                $this->request->getPost()->has('remember')
            );
            if ($result == 1) {
                if ($this->request->getParameters()->has('redirect')) {
                    return $this->redirect()->temporary(base64_decode($this->request->getParameters()->get('redirect')));
                }

                return $this->redirect()->toNewPage(ROOT_DIR);
            }

            return [
                'error_msg' => $this->get('core.helpers.alerts')->errorBox($this->lang->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong'))
            ];
        }
    }

    /**
     * @param string $last
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionLogout($last = '')
    {
        $this->user->logout();

        if (!empty($last)) {
            $lastPage = base64_decode($last);

            if (!preg_match('/^((acp|users)\/)/', $lastPage)) {
                return $this->redirect()->temporary($lastPage);
            }
        }
        return $this->redirect()->toNewPage(ROOT_DIR);
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionRegister()
    {
        $settings = $this->config->getSettings('users');

        if ($this->user->isAuthenticated() === true) {
            return $this->redirect()->toNewPage(ROOT_DIR);
        } elseif ($settings['enable_registration'] == 0) {
            $this->setContent($this->get('core.helpers.alerts')->errorBox($this->lang->t('users', 'user_registration_disabled')));
        } else {
            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_registerPost($this->request->getPost()->all(), $settings);
            }

            $defaults = [
                'nickname' => '',
                'mail' => '',
            ];

            if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', $this->captchaHelpers->captcha());
            }

            $this->formTokenHelper->generateFormToken();

            return [
                'form' => array_merge($defaults, $this->request->getPost()->all())
            ];
        }
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionViewProfile($id)
    {
        if ($this->userRepository->resultExists($id) === true) {
            $user = $this->user->getUserInfo($id);
            $user['gender'] = str_replace([1, 2, 3], ['', $this->lang->t('users', 'female'), $this->lang->t('users', 'male')], $user['gender']);

            return [
                'user' => $user
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _forgotPasswordPost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->usersValidator->validateForgotPassword($formData);

                // Neues Passwort und neuen Zufallsschlüssel erstellen
                $newPassword = $this->secureHelper->salt(Core\User::SALT_LENGTH);
                $host = $this->request->getHostname();

                // Je nachdem, wie das Feld ausgefüllt wurde, dieses auswählen
                if ($this->get('core.validation.validation_rules.email_validation_rule')->isValid($formData['nick_mail']) === true &&
                    $this->userRepository->resultExistsByEmail($formData['nick_mail']) === true
                ) {
                    $user = $this->userRepository->getOneByEmail($formData['nick_mail']);
                } else {
                    $user = $this->userRepository->getOneByNickname($formData['nick_mail']);
                }

                $seoSettings = $this->config->getSettings('seo');

                // E-Mail mit dem neuen Passwort versenden
                $subject = str_replace(['{title}', '{host}'], [$seoSettings['title'], $host], $this->lang->t('users', 'forgot_pwd_mail_subject'));
                $search = ['{name}', '{mail}', '{password}', '{title}', '{host}'];
                $replace = [$user['nickname'], $user['mail'], $newPassword, $seoSettings['title'], $host];
                $body = str_replace($search, $replace, $this->lang->t('users', 'forgot_pwd_mail_message'));

                $settings = $this->config->getSettings('users');
                $mailIsSent = $this->sendEmail->execute(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

                // Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
                if ($mailIsSent === true) {
                    $salt = $this->secureHelper->salt(Core\User::SALT_LENGTH);
                    $updateValues = [
                        'pwd' => $this->secureHelper->generateSaltedPassword($salt, $newPassword, 'sha512'),
                        'pwd_salt' => $salt,
                        'login_errors' => 0
                    ];
                    $bool = $this->userRepository->update($updateValues, $user['id']);
                }

                $this->formTokenHelper->unsetFormToken();

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox(
                    $this->lang->t('users', $mailIsSent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'),
                    ROOT_DIR
                ));
            },
            $this->request->getFullPath()
        );
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _registerPost(array $formData, array $settings)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData, $settings) {
                $this->usersValidator->validateRegistration($formData);

                $systemSettings = $this->config->getSettings('system');
                $seoSettings = $this->config->getSettings('seo');

                // E-Mail mit den Accountdaten zusenden
                $subject = str_replace(
                    ['{title}', '{host}'],
                    [$seoSettings['title'], $this->request->getHostname()],
                    $this->lang->t('users', 'register_mail_subject')
                );
                $body = str_replace(
                    ['{name}', '{mail}', '{password}', '{title}', '{host}'],
                    [$formData['nickname'], $formData['mail'], $formData['pwd'], $seoSettings['title'], $this->request->getHostname()],
                    $this->lang->t('users', 'register_mail_message')
                );
                $mailIsSent = $this->sendEmail->execute('', $formData['mail'], $settings['mail'], $subject, $body);

                $salt = $this->secureHelper->salt(Core\User::SALT_LENGTH);
                $insertValues = [
                    'id' => '',
                    'nickname' => Core\Functions::strEncode($formData['nickname']),
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
                    $this->lang->t('users', $mailIsSent === true && $lastId !== false && $bool2 !== false ? 'register_success' : 'register_error'),
                    ROOT_DIR
                ));
            },
            $this->request->getFullPath()
        );
    }
}
