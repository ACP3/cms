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
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    /**
     * @param Core\Context\Frontend $context
     * @param Core\Helpers\Secure $secureHelper
     * @param Newsletter\Model $newsletterModel
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Helpers\Secure $secureHelper,
        Newsletter\Model $newsletterModel)
    {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->newsletterModel = $newsletterModel;
    }

    public function actionActivate()
    {
        try {
            $mail = $hash = '';
            if ($this->get('core.validator.rules.misc')->email($this->request->mail) && $this->get('core.validator.rules.misc')->isMD5($this->request->hash)) {
                $mail = $this->request->mail;
                $hash = $this->request->hash;
            }

            /** @var \ACP3\Modules\Newsletter\Validator $validator */
            $validator = $this->get('newsletter.validator');
            $validator->validateActivate($mail, $hash);

            $bool = $this->newsletterModel->update(array('hash' => ''), array('mail' => $mail, 'hash' => $hash), Newsletter\Model::TABLE_NAME_ACCOUNTS);

            $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->setContent($this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $this->_indexPost($_POST);
        }

        $this->view->assign('form', array_merge(array('mail' => ''), $_POST));

        $field_value = $this->request->action ? $this->request->action : 'subscribe';

        $actions_Lang = array(
            $this->lang->t('newsletter', 'subscribe'),
            $this->lang->t('newsletter', 'unsubscribe')
        );
        $this->view->assign('actions', $this->get('core.helpers.forms')->selectGenerator('action', array('subscribe', 'unsubscribe'), $actions_Lang, $field_value, 'checked'));

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha());
        }

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\ResultNotExists
     */
    private function _indexPost(array $formData)
    {
        try {
            $validator = $this->get('newsletter.validator');
            switch ($this->request->action) {
                case 'subscribe':
                    $validator->validateSubscribe($formData);

                    $bool = $this->get('newsletter.helpers')->subscribeToNewsletter($formData['mail']);

                    $this->secureHelper->unsetFormToken($this->request->query);

                    $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
                    break;
                case 'unsubscribe':
                    $validator->validateUnsubscribe($formData);

                    $bool = $this->newsletterModel->delete($formData['mail'], 'mail', Newsletter\Model::TABLE_NAME_ACCOUNTS);

                    $this->secureHelper->unsetFormToken($this->request->query);

                    $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
                    break;
                default:
                    throw new Core\Exceptions\ResultNotExists();
            }
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->setContent($this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

}