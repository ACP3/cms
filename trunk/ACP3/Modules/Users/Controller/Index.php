<?php

namespace ACP3\Modules\Users\Controller;

use ACP3\Core;
use ACP3\Modules\Users;
use ACP3\Modules\Permissions;

/**
 * Class Index
 * @package ACP3\Modules\Users\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Users\Model
     */
    protected $usersModel;
    /**
     * @var Core\Config
     */
    protected $usersConfig;
    /**
     * @var Permissions\Model
     */
    protected $permissionsModel;

    /**
     * @param Core\Context\Frontend $context
     * @param Core\Date $date
     * @param Core\Pagination $pagination
     * @param Core\Helpers\Secure $secureHelper
     * @param Users\Model $usersModel
     * @param Core\Config $usersConfig
     * @param Permissions\Model $permissionsModel
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\Secure $secureHelper,
        Users\Model $usersModel,
        Core\Config $usersConfig,
        Permissions\Model $permissionsModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->secureHelper = $secureHelper;
        $this->usersModel = $usersModel;
        $this->usersConfig = $usersConfig;
        $this->permissionsModel = $permissionsModel;
    }

    public function actionForgotPwd()
    {
        if ($this->auth->isUser() === true) {
            $this->redirect()->toNewPage(ROOT_DIR);
        } else {
            if (empty($_POST) === false) {
                $this->_forgotPasswordPost($_POST);
            }

            $this->view->assign('form', array_merge(array('nick_mail' => ''), $_POST));

            if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', $this->get('captcha.helpers')->captcha());
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
                $alerts = $this->get('core.helpers.alerts');
                $this->view->assign('error_msg', $alerts->errorBox($this->lang->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong')));
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
                $this->_registerPost($_POST);
            }

            $defaults = array(
                'nickname' => '',
                'mail' => '',
            );

            $this->view->assign('form', array_merge($defaults, $_POST));

            if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', $this->get('captcha.helpers')->captcha());
            }

            $this->secureHelper->generateFormToken($this->request->query);
        }
    }

    public function actionViewProfile()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true && $this->usersModel->resultExists($this->request->id) === true) {
            $user = $this->auth->getUserInfo($this->request->id);
            $user['gender'] = str_replace(array(1, 2, 3), array('', $this->lang->t('users', 'female'), $this->lang->t('users', 'male')), $user['gender']);
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
            $validator = $this->get('users.validator');
            $validator->validateForgotPassword($formData);

            // Neues Passwort und neuen Zufallsschlüssel erstellen
            $newPassword = $this->secureHelper->salt(8);
            $host = htmlentities($_SERVER['HTTP_HOST']);

            // Je nachdem, wie das Feld ausgefüllt wurde, dieses auswählen
            if ($this->get('core.validator.rules.misc')->email($formData['nick_mail']) === true && $this->usersModel->resultExistsByEmail($formData['nick_mail']) === true) {
                $user = $this->usersModel->getOneByEmail($formData['nick_mail']);
            } else {
                $user = $this->usersModel->getOneByNickname($formData['nick_mail']);
            }

            // E-Mail mit dem neuen Passwort versenden
            $subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'forgot_pwd_mail_subject'));
            $search = array('{name}', '{mail}', '{password}', '{title}', '{host}');
            $replace = array($user['nickname'], $user['mail'], $newPassword, CONFIG_SEO_TITLE, $host);
            $body = str_replace($search, $replace, $this->lang->t('users', 'forgot_pwd_mail_message'));

            $settings = $this->usersConfig->getSettings();
            $mailIsSent = $this->get('core.functions')->generateEmail(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

            // Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
            if ($mailIsSent === true) {
                $salt = $this->secureHelper->salt(12);
                $updateValues = array(
                    'pwd' => $this->secureHelper->generateSaltedPassword($salt, $newPassword) . ':' . $salt,
                    'login_errors' => 0
                );
                $bool = $this->usersModel->update($updateValues, $user['id']);
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->setContent($this->get('core.helpers.alerts')->confirmBox($this->lang->t('users', $mailIsSent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'), ROOT_DIR));
            return;
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'users/forgot_pwd');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    private function _registerPost(array $formData)
    {
        try {
            $validator = $this->get('users.validator');
            $validator->validateRegistration($formData);

            // E-Mail mit den Accountdaten zusenden
            $host = htmlentities($_SERVER['HTTP_HOST']);
            $subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'register_mail_subject'));
            $body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($formData['nickname'], $formData['mail'], $formData['pwd'], CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'register_mail_message'));
            $mailIsSent = $this->get('core.functions')->generateEmail('', $formData['mail'], $settings['mail'], $subject, $body);

            $salt = $this->secureHelper->salt(12);
            $insertValues = array(
                'id' => '',
                'nickname' => Core\Functions::strEncode($formData['nickname']),
                'pwd' => $this->secureHelper->generateSaltedPassword($salt, $formData['pwd']) . ':' . $salt,
                'mail' => $formData['mail'],
                'date_format_long' => CONFIG_DATE_FORMAT_LONG,
                'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
                'time_zone' => CONFIG_DATE_TIME_ZONE,
                'language' => CONFIG_LANG,
                'entries' => CONFIG_ENTRIES,
                'registration_date' => $this->date->getCurrentDateTime(),
            );

            $lastId = $this->usersModel->insert($insertValues);
            $bool2 = $this->permissionsModel->insert(array('user_id' => $lastId, 'role_id' => 2), Permissions\Model::TABLE_NAME_USER_ROLES);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->setContent($this->get('core.helpers.alerts')->confirmBox($this->lang->t('users', $mailIsSent === true && $lastId !== false && $bool2 !== false ? 'register_success' : 'register_error'), ROOT_DIR));
            return;
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'users/register');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

}