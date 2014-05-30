<?php

namespace ACP3\Modules\Contact\Controller;

use ACP3\Core;
use ACP3\Modules\Contact;

/**
 * Description of ContactFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Contact\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Contact\Model($this->db, $this->lang, $this->auth);
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validate($_POST);

                $settings = Core\Config::getSettings('contact');
                $_POST['message'] = Core\Functions::strEncode($_POST['message'], true);

                $subject = sprintf($this->lang->t('contact', 'contact_subject'), CONFIG_SEO_TITLE);
                $body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($_POST['name'], $_POST['mail'], $_POST['message'], "\n"), $this->lang->t('contact', 'contact_body'));
                $bool = Core\Functions::generateEmail('', $settings['mail'], $_POST['mail'], $subject, $body);

                // Nachrichtenkopie an Absender senden
                if (isset($_POST['copy'])) {
                    $subjectCopy = sprintf($this->lang->t('contact', 'sender_subject'), CONFIG_SEO_TITLE);
                    $bodyCopy = sprintf($this->lang->t('contact', 'sender_body'), CONFIG_SEO_TITLE, $_POST['message']);
                    Core\Functions::generateEmail($_POST['name'], $_POST['mail'], $settings['mail'], $subjectCopy, $bodyCopy);
                }

                $this->session->unsetFormToken();

                $this->setContent(Core\Functions::confirmBox($bool === true ? $this->lang->t('contact', 'send_mail_success') : $this->lang->t('contact', 'send_mail_error'), $this->uri->route('contact')));
                return;
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'contact');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $defaults = array(
            'name' => '',
            'name_disabled' => '',
            'mail' => '',
            'mail_disabled' => '',
            'message' => '',
        );

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->auth->isUser() === true) {
            $user = $this->auth->getUserInfo();
            $disabled = ' readonly="readonly" class="readonly"';
            $defaults['name'] = !empty($user['realname']) ? $user['realname'] : $user['nickname'];
            $defaults['name_disabled'] = $disabled;
            $defaults['mail'] = $user['mail'];
            $defaults['mail_disabled'] = $disabled;
        }

        $this->view->assign('form', array_merge($defaults, $_POST));
        $this->view->assign('copy_checked', Core\Functions::selectEntry('copy', 1, 0, 'checked'));

        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
        }

        $this->session->generateFormToken();
    }

    public function actionImprint()
    {
        $settings = Core\Config::getSettings('contact');
        $settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
        $settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
        $this->view->assign('imprint', $settings);

        $this->view->assign('powered_by', sprintf($this->lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));
    }

    public function actionSidebar()
    {
        $settings = Core\Config::getSettings('contact');
        $settings['address'] = Core\Functions::rewriteInternalUri($settings['address']);
        $settings['disclaimer'] = Core\Functions::rewriteInternalUri($settings['disclaimer']);
        $this->view->assign('sidebar_contact', $settings);

        $this->view->displayTemplate('contact/sidebar.tpl');
    }

}