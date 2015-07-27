<?php

namespace ACP3\Modules\ACP3\Newsletter\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Newsletter\Controller
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helpers
     */
    protected $newsletterHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model
     */
    protected $newsletterModel;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validator
     */
    protected $newsletterValidator;
    /**
     * @var \ACP3\Modules\ACP3\Captcha\Helpers
     */
    protected $captchaHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Helpers\FormToken                  $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Helpers         $newsletterHelpers
     * @param \ACP3\Modules\ACP3\Newsletter\Model           $newsletterModel
     * @param \ACP3\Modules\ACP3\Newsletter\Validator       $newsletterValidator
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Helpers $newsletterHelpers,
        Newsletter\Model $newsletterModel,
        Newsletter\Validator $newsletterValidator)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->newsletterHelpers = $newsletterHelpers;
        $this->newsletterModel = $newsletterModel;
        $this->newsletterValidator = $newsletterValidator;
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
     * @param string $mail
     * @param string $hash
     */
    public function actionActivate($mail, $hash)
    {
        try {
            $this->newsletterValidator->validateActivate($mail, $hash);

            $bool = $this->newsletterModel->update(['hash' => ''], ['mail' => $mail, 'hash' => $hash], Newsletter\Model::TABLE_NAME_ACCOUNTS);

            $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->setContent($this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionIndex($action = 'subscribe')
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_indexPost($this->request->getPost()->getAll(), $action);
        }

        $this->view->assign('form', array_merge(['mail' => ''], $this->request->getPost()->getAll()));

        $fieldValue = $action;

        $actions_Lang = [
            $this->lang->t('newsletter', 'subscribe'),
            $this->lang->t('newsletter', 'unsubscribe')
        ];
        $this->view->assign('actions', $this->get('core.helpers.forms')->selectGenerator('action', ['subscribe', 'unsubscribe'], $actions_Lang, $fieldValue, 'checked'));

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha());
        }

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array  $formData
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    protected function _indexPost(array $formData, $action)
    {
        $this->handlePostAction(
            function() use ($formData, $action) {
                switch ($action) {
                    case 'subscribe':
                        $this->newsletterValidator->validateSubscribe($formData);

                        $bool = $this->newsletterHelpers->subscribeToNewsletter($formData['mail']);

                        $this->formTokenHelper->unsetFormToken($this->request->getQuery());

                        $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
                        break;
                    case 'unsubscribe':
                        $this->newsletterValidator->validateUnsubscribe($formData);

                        $bool = $this->newsletterModel->delete(['mail' => $formData['mail']], '', Newsletter\Model::TABLE_NAME_ACCOUNTS);

                        $this->formTokenHelper->unsetFormToken($this->request->getQuery());

                        $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
                        break;
                    default:
                        throw new Core\Exceptions\ResultNotExists();
                }
            }
        );
    }
}
