<?php

namespace ACP3\Modules\Users\Controller;

use ACP3\Core;
use ACP3\Modules\Users;

/**
 * Description of UsersFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Users\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Users\Model($this->db);
    }

    public function actionForgotPwd()
    {
        if ($this->auth->isUser() === true) {
            $this->uri->redirect(0, ROOT_DIR);
        } else {
            if (empty($_POST) === false) {
                try {
                    $validator = new Users\Validator($this->lang, $this->auth, $this->uri, $this->model);
                    $validator->validateForgotPassword($_POST);

                    // Neues Passwort und neuen Zufallsschlüssel erstellen
                    $securityHelper = new Core\Helpers\Secure();
                    $newPassword = $securityHelper->salt(8);
                    $host = htmlentities($_SERVER['HTTP_HOST']);

                    // Je nachdem, wie das Feld ausgefüllt wurde, dieses auswählen
                    if (Core\Validate::email($_POST['nick_mail']) === true && $this->model->resultExistsByEmail($_POST['nick_mail']) === true) {
                        $user = $this->model->getOneByEmail($_POST['nick_mail']);
                    } else {
                        $user = $this->model->getOneByNickname($_POST['nick_mail']);
                    }

                    // E-Mail mit dem neuen Passwort versenden
                    $subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'forgot_pwd_mail_subject'));
                    $search = array('{name}', '{mail}', '{password}', '{title}', '{host}');
                    $replace = array($user['nickname'], $user['mail'], $newPassword, CONFIG_SEO_TITLE, $host);
                    $body = str_replace($search, $replace, $this->lang->t('users', 'forgot_pwd_mail_message'));

                    $config = new Core\Config($this->db, 'users');
                    $settings = $config->getSettings();
                    $mailIsSent = Core\Functions::generateEmail(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

                    // Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
                    if ($mailIsSent === true) {
                        $securityHelper = new Core\Helpers\Secure();

                        $salt = $securityHelper->salt(12);
                        $updateValues = array(
                            'pwd' => $securityHelper->generateSaltedPassword($salt, $newPassword) . ':' . $salt,
                            'login_errors' => 0
                        );
                        $bool = $this->model->update($updateValues, $user['id']);
                    }

                    $this->session->unsetFormToken();

                    $this->setContent(Core\Functions::confirmBox($this->lang->t('users', $mailIsSent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'), ROOT_DIR));
                    return;
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'users/forgot_pwd');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('form', array_merge(array('nick_mail' => ''), $_POST));

            if (Core\Modules::hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
            }

            $this->session->generateFormToken();
        }
    }

    public function actionIndex()
    {
        $users = $this->model->getAll(POS, $this->auth->entries);
        $c_users = count($users);
        $allUsers = $this->model->countAll();

        if ($c_users > 0) {
            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $allUsers
            );
            $pagination->display();

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
            $this->uri->redirect(0, ROOT_DIR);
        } elseif (empty($_POST) === false) {
            $result = $this->auth->login(Core\Functions::strEncode($_POST['nickname']), $_POST['pwd'], isset($_POST['remember']) ? 31104000 : 3600);
            if ($result == 1) {
                if ($this->uri->redirect) {
                    $this->uri->redirect(base64_decode($this->uri->redirect));
                } else {
                    $this->uri->redirect(0, ROOT_DIR);
                }
            } else {
                $this->view->assign('error_msg', Core\Functions::errorBox($this->lang->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong')));
            }
        }
    }

    public function actionLogout()
    {
        $this->auth->logout();

        if ($this->uri->last) {
            $lastPage = base64_decode($this->uri->last);
            if (!preg_match('/^((acp|users)\/)/', $lastPage))
                $this->uri->redirect($lastPage);
        }
        $this->uri->redirect(0, ROOT_DIR);
    }

    public function actionRegister()
    {
        $config = new Core\Config($this->db, 'users');
        $settings = $config->getSettings();

        if ($this->auth->isUser() === true) {
            $this->uri->redirect(0, ROOT_DIR);
        } elseif ($settings['enable_registration'] == 0) {
            $this->setContent(Core\Functions::errorBox($this->lang->t('users', 'user_registration_disabled')));
        } else {
            if (empty($_POST) === false) {
                try {
                    $validator = new Users\Validator($this->lang, $this->auth, $this->uri, $this->model);
                    $validator->validateRegistration($_POST);

                    // E-Mail mit den Accountdaten zusenden
                    $host = htmlentities($_SERVER['HTTP_HOST']);
                    $subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'register_mail_subject'));
                    $body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($_POST['nickname'], $_POST['mail'], $_POST['pwd'], CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'register_mail_message'));
                    $mailIsSent = Core\Functions::generateEmail('', $_POST['mail'], $settings['mail'], $subject, $body);

                    $securityHelper = new Core\Helpers\Secure();
                    $salt = $securityHelper->salt(12);
                    $insertValues = array(
                        'id' => '',
                        'nickname' => Core\Functions::strEncode($_POST['nickname']),
                        'pwd' => $securityHelper->generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
                        'mail' => $_POST['mail'],
                        'date_format_long' => CONFIG_DATE_FORMAT_LONG,
                        'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
                        'time_zone' => CONFIG_DATE_TIME_ZONE,
                        'language' => CONFIG_LANG,
                        'entries' => CONFIG_ENTRIES,
                        'registration_date' => $this->date->getCurrentDateTime(),
                    );

                    $this->db->beginTransaction();
                    try {
                        $lastId = $this->model->insert($insertValues);
                        $bool2 = $this->db->insert(DB_PRE . 'acl_user_roles', array('user_id' => $lastId, 'role_id' => 2));
                        $this->db->commit();
                    } catch (\Exception $e) {
                        $this->db->rollback();
                        $lastId = $bool2 = false;
                    }

                    $this->session->unsetFormToken();

                    $this->setContent(Core\Functions::confirmBox($this->lang->t('users', $mailIsSent === true && $lastId !== false && $bool2 !== false ? 'register_success' : 'register_error'), ROOT_DIR));
                    return;
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'users/register');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $defaults = array(
                'nickname' => '',
                'mail' => '',
            );

            $this->view->assign('form', array_merge($defaults, $_POST));

            if (Core\Modules::hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
            }

            $this->session->generateFormToken();
        }
    }

    public function actionViewProfile()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->resultExists($this->uri->id) === true) {
            $user = $this->auth->getUserInfo($this->uri->id);
            $user['gender'] = str_replace(array(1, 2, 3), array('', $this->lang->t('users', 'female'), $this->lang->t('users', 'male')), $user['gender']);
            $user['birthday'] = $this->date->format($user['birthday'], $user['birthday_display'] == 1 ? 'd.m.Y' : 'd.m.');
            if (!empty($user['website']) && (bool)preg_match('=^http(s)?://=', $user['website']) === false) {
                $user['website'] = 'http://' . $user['website'];
            }

            $this->view->assign('user', $user);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}