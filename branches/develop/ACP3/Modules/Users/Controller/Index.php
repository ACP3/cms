<?php

namespace ACP3\Modules\Users\Controller;

use ACP3\Core;
use ACP3\Modules\Captcha;
use ACP3\Modules\Users;
use ACP3\Modules\Permissions;

/**
 * Class Index
 * @package ACP3\Modules\Users\Controller
 */
class Index extends Core\Modules\Controller\Frontend
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
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\Users\Model
     */
    protected $usersModel;
    /**
     * @var \ACP3\Modules\Users\Validator
     */
    protected $usersValidator;
    /**
     * @var \ACP3\Core\Config
     */
    protected $usersConfig;
    /**
     * @var \ACP3\Modules\Permissions\Model
     */
    protected $permissionsModel;
    /**
     * @var \ACP3\Core\Config
     */
    protected $seoConfig;
    /**
     * @var \ACP3\Core\Helpers\SendEmail
     */
    protected $sendEmail;
    /**
     * @var \ACP3\Modules\Captcha\Helpers
     */
    protected $captchaHelpers;

    /**
     * @param \ACP3\Core\Context\Frontend     $context
     * @param \ACP3\Core\Date                 $date
     * @param \ACP3\Core\Pagination           $pagination
     * @param \ACP3\Core\Helpers\Secure       $secureHelper
     * @param \ACP3\Modules\Users\Model       $usersModel
     * @param \ACP3\Modules\Users\Validator   $usersValidator
     * @param \ACP3\Core\Config               $usersConfig
     * @param \ACP3\Modules\Permissions\Model $permissionsModel
     * @param \ACP3\Core\Config               $seoConfig
     * @param \ACP3\Core\Helpers\SendEmail    $sendEmail
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\Secure $secureHelper,
        Users\Model $usersModel,
        Users\Validator $usersValidator,
        Core\Config $usersConfig,
        Permissions\Model $permissionsModel,
        Core\Config $seoConfig,
        Core\Helpers\SendEmail $sendEmail)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->secureHelper = $secureHelper;
        $this->usersModel = $usersModel;
        $this->usersValidator = $usersValidator;
        $this->usersConfig = $usersConfig;
        $this->permissionsModel = $permissionsModel;
        $this->seoConfig = $seoConfig;
        $this->sendEmail = $sendEmail;
    }

    /**
     * @param \ACP3\Modules\Captcha\Helpers $captchaHelprs
     *
     * @return $this
     */
    public function setCaptchaHelpers(Captcha\Helpers $captchaHelprs)
    {
        $this->captchaHelpers = $captchaHelprs;

        return $this;
    }

    public function actionForgotPwd()
    {
        if ($this->auth->isUser() === true) {
            $this->redirect()->toNewPage(ROOT_DIR);
        } else {
            if (empty($_POST) === false) {
                $this->_forgotPasswordPost($_POST);
            }

            $this->view->assign('form', array_merge(['nick_mail' => ''], $_POST));

            if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', $this->captchaHelpers->captcha());
            }

            $this->secureHelper->generateFormToken($this->request->query);
        }
    }

    public function actionIndex()
    {
        $users = $this->usersModel->getAll(POS, $this->auth->entries);
        $c_users = count($users);
        $allUsers = $this->usersModel->countAll();

        if ($c_users > 0) {
            $this->pagination->setTotalResults($allUsers);
            $this->pagination->display();

            for ($i = 0; $i < $c_users; ++$i) {
                if (!empty($users[$i]['website']) && (bool)preg_match('=^http(s)?://=', $users[$i]['website']) === false) {
                    $users[$i]['website'] = 'http://' . $users[$i]['website'];
                }
            }
            $this->view->assign('users', $users);
        }
        $this->view->assign('LANG_users_found', sprintf($this->lang->t('users', 'users_found'), $allUsers));
    }

    public function actionLogin()
    {
        // Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
        if ($this->auth->isUser() === true) {
            $this->redirect()->toNewPage(ROOT_DIR);
        } elseif (empty($_POST) === false) {
            $result = $this->auth->login(Core\Functions::strEncode($_POST['nickname']), $_POST['pwd'], isset($_POST['remember']) ? 31104000 : 3600);
            if ($result == 1) {
                if ($this->request->redirect) {
                    $this->redirect()->temporary(base64_decode($this->request->redirect));
                } else {
                    $this->redirect()->toNewPage(ROOT_DIR);
                }
            } else {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($this->lang->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong')));
            }
        }
    }

    public function actionLogout()
    {
        $this->auth->logout();

        if ($this->request->last) {
            $lastPage = base64_decode($this->request->last);

            if (!preg_match('/^((acp|users)\/)/', $lastPage)) {
                $this->redirect()->temporary($lastPage);
            }
        }
        $this->redirect()->toNewPage(ROOT_DIR);
    }

    public function actionRegister()
    {
        $settings = $this->usersConfig->getSettings();

        if ($this->auth->isUser() === true) {
            $this->redirect()->toNewPage(ROOT_DIR);
        } elseif ($settings['enable_registration'] == 0) {
            $this->setContent($this->get('core.helpers.alerts')->errorBox($this->lang->t('users', 'user_registration_disabled')));
        } else {
            if (empty($_POST) === false) {
                $this->_registerPost($_POST, $settings);
            }

            $defaults = [
                'nickname' => '',
                'mail' => '',
            ];

            $this->view->assign('form', array_merge($defaults, $_POST));

            if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', $this->captchaHelpers->captcha());
            }

            $this->secureHelper->generateFormToken($this->request->query);
        }
    }

    public function actionViewProfile()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true && $this->usersModel->resultExists($this->request->id) === true) {
            $user = $this->auth->getUserInfo($this->request->id);
            $user['gender'] = str_replace([1, 2, 3], ['', $this->lang->t('users', 'female'), $this->lang->t('users', 'male')], $user['gender']);
            if (!empty($user['website']) && (bool)preg_match('=^http(s)?://=', $user['website']) === false) {
                $user['website'] = 'http://' . $user['website'];
            }

            $this->view->assign('user', $user);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    private function _forgotPasswordPost(array $formData)
    {
        try {
            $this->usersValidator->validateForgotPassword($formData);

            // Neues Passwort und neuen Zufallsschlüssel erstellen
            $newPassword = $this->secureHelper->salt(8);
            $host = htmlentities($_SERVER['HTTP_HOST']);

            // Je nachdem, wie das Feld ausgefüllt wurde, dieses auswählen
            if ($this->get('core.validator.rules.misc')->email($formData['nick_mail']) === true && $this->usersModel->resultExistsByEmail($formData['nick_mail']) === true) {
                $user = $this->usersModel->getOneByEmail($formData['nick_mail']);
            } else {
                $user = $this->usersModel->getOneByNickname($formData['nick_mail']);
            }

            $seoSettings = $this->seoConfig->getSettings();

            // E-Mail mit dem neuen Passwort versenden
            $subject = str_replace(['{title}', '{host}'], [$seoSettings['title'], $host], $this->lang->t('users', 'forgot_pwd_mail_subject'));
            $search = ['{name}', '{mail}', '{password}', '{title}', '{host}'];
            $replace = [$user['nickname'], $user['mail'], $newPassword, $seoSettings['title'], $host];
            $body = str_replace($search, $replace, $this->lang->t('users', 'forgot_pwd_mail_message'));

            $settings = $this->usersConfig->getSettings();
            $mailIsSent = $this->sendEmail->execute(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

            // Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
            if ($mailIsSent === true) {
                $salt = $this->secureHelper->salt(12);
                $updateValues = [
                    'pwd' => $this->secureHelper->generateSaltedPassword($salt, $newPassword) . ':' . $salt,
                    'login_errors' => 0
                ];
                $bool = $this->usersModel->update($updateValues, $user['id']);
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('users', $mailIsSent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'), ROOT_DIR));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'users/forgot_pwd');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    private function _registerPost(array $formData, array $settings)
    {
        try {
            $this->usersValidator->validateRegistration($formData);

            $systemSettings = $this->systemConfig->getSettings();
            $seoSettings = $this->seoConfig->getSettings();

            // E-Mail mit den Accountdaten zusenden
            $host = htmlentities($_SERVER['HTTP_HOST']);
            $subject = str_replace(
                ['{title}', '{host}'],
                [$seoSettings['title'], $host],
                $this->lang->t('users', 'register_mail_subject')
            );
            $body = str_replace(
                ['{name}', '{mail}', '{password}', '{title}', '{host}'],
                [$formData['nickname'], $formData['mail'], $formData['pwd'], $seoSettings['title'], $host],
                $this->lang->t('users', 'register_mail_message')
            );
            $mailIsSent = $this->sendEmail->execute('', $formData['mail'], $settings['mail'], $subject, $body);

            $salt = $this->secureHelper->salt(12);
            $insertValues = [
                'id' => '',
                'nickname' => Core\Functions::strEncode($formData['nickname']),
                'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd']) . ':' . $salt,
                'mail' => $formData['mail'],
                'date_format_long' => $systemSettings['date_format_long'],
                'date_format_short' => $systemSettings['date_format_short'],
                'time_zone' => $systemSettings['date_time_zone'],
                'language' => $systemSettings['lang'],
                'entries' => $systemSettings['entries'],
                'registration_date' => $this->date->getCurrentDateTime(),
            ];

            $lastId = $this->usersModel->insert($insertValues);
            $bool2 = $this->permissionsModel->insert(['user_id' => $lastId, 'role_id' => 2], Permissions\Model::TABLE_NAME_USER_ROLES);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('users', $mailIsSent === true && $lastId !== false && $bool2 !== false ? 'register_success' : 'register_error'), ROOT_DIR));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'users/register');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
