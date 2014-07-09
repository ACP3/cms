<?php

namespace ACP3\Modules\Newsletter\Controller;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Description of NewsletterFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{

    /**
     *
     * @var Newsletter\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Newsletter\Model($this->db);
    }

    public function actionActivate()
    {
        try {
            $mail = $hash = '';
            if ($this->get('core.validate')->email($this->uri->mail) && $this->get('core.validate')->isMD5($this->uri->hash)) {
                $mail = $this->uri->mail;
                $hash = $this->uri->hash;
            }

            $validator = $this->get('newsletter.validator');
            $validator->validateActivate($mail, $hash);

            $bool = $this->model->update(array('hash' => ''), array('mail' => $mail, 'hash' => $hash), Newsletter\Model::TABLE_NAME_ACCOUNTS);

            $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
            $this->setContent($alerts->confirmBox($this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
        } catch (Core\Exceptions\ValidationFailed $e) {
            $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
            $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
        }
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            try {
                $validator = $this->get('newsletter.validator');
                switch ($this->uri->action) {
                    case 'subscribe':
                        $validator->validateSubscribe($_POST);

                        $bool = $this->get('newsletter.helpers')->subscribeToNewsletter($_POST['mail']);

                        $this->session->unsetFormToken();

                        $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                        $this->setContent($alerts->confirmBox($this->lang->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
                        return;
                    case 'unsubscribe':
                        $validator->validateUnsubscribe($_POST);

                        $bool = $this->model->delete($_POST['mail'], 'mail', Newsletter\Model::TABLE_NAME_ACCOUNTS);

                        $this->session->unsetFormToken();

                        $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                        $this->setContent($alerts->confirmBox($this->lang->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
                        return;
                    default:
                        throw new Core\Exceptions\ResultNotExists();
                }
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->setContent($alerts->errorBox($e->getMessage()));
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $this->view->assign('form', array_merge(array('mail' => ''), $_POST));

        $field_value = $this->uri->action ? $this->uri->action : 'subscribe';

        $actions_Lang = array(
            $this->lang->t('newsletter', 'subscribe'),
            $this->lang->t('newsletter', 'unsubscribe')
        );
        $this->view->assign('actions', Core\Functions::selectGenerator('action', array('subscribe', 'unsubscribe'), $actions_Lang, $field_value, 'checked'));

        if ($this->modules->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha());
        }

        $this->session->generateFormToken();
    }

}