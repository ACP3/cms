<?php

namespace ACP3\Modules\Newsletter\Controller;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\Newsletter\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    public function __construct(
        Core\Context\Frontend $context,
        \Doctrine\DBAL\Connection $db,
        Core\Helpers\Secure $secureHelper,
        Newsletter\Model $newsModel)
    {
        parent::__construct($context);

        $this->db = $db;
        $this->secureHelper = $secureHelper;
        $this->newsletterModel = $newsModel;
    }

    public function actionActivate()
    {
        try {
            $mail = $hash = '';
            if ($this->get('core.validator.rules.misc')->email($this->request->mail) && $this->get('core.validator.rules.misc')->isMD5($this->request->hash)) {
                $mail = $this->request->mail;
                $hash = $this->request->hash;
            }

            $validator = $this->get('newsletter.validator');
            $validator->validateActivate($mail, $hash);

            $bool = $this->newsletterModel->update(array('hash' => ''), array('mail' => $mail, 'hash' => $hash), Newsletter\Model::TABLE_NAME_ACCOUNTS);

            $this->setContent($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            try {
                $validator = $this->get('newsletter.validator');
                switch ($this->request->action) {
                    case 'subscribe':
                        $validator->validateSubscribe($_POST);

                        $bool = $this->get('newsletter.helpers')->subscribeToNewsletter($_POST['mail']);

                        $this->secureHelper->unsetFormToken();

                        $this->setContent($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
                        return;
                    case 'unsubscribe':
                        $validator->validateUnsubscribe($_POST);

                        $bool = $this->newsletterModel->delete($_POST['mail'], 'mail', Newsletter\Model::TABLE_NAME_ACCOUNTS);

                        $this->secureHelper->unsetFormToken();

                        $this->setContent($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
                        return;
                    default:
                        throw new Core\Exceptions\ResultNotExists();
                }
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->setContent($this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        $this->view->assign('form', array_merge(array('mail' => ''), $_POST));

        $field_value = $this->request->action ? $this->request->action : 'subscribe';

        $actions_Lang = array(
            $this->lang->t('newsletter', 'subscribe'),
            $this->lang->t('newsletter', 'unsubscribe')
        );
        $this->view->assign('actions', Core\Functions::selectGenerator('action', array('subscribe', 'unsubscribe'), $actions_Lang, $field_value, 'checked'));

        if ($this->modules->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha());
        }

        $this->secureHelper->generateFormToken($this->request->query);
    }

}