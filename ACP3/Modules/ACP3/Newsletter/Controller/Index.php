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
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe
     */
    protected $subscribeHelper;
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
     * @param \ACP3\Core\Modules\Controller\FrontendContext  $context
     * @param \ACP3\Core\Helpers\FormToken                   $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe $subscribeHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Model            $newsletterModel
     * @param \ACP3\Modules\ACP3\Newsletter\Validator        $newsletterValidator
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Helper\Subscribe $subscribeHelper,
        Newsletter\Model $newsletterModel,
        Newsletter\Validator $newsletterValidator)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->subscribeHelper = $subscribeHelper;
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
     * @param string $hash
     */
    public function actionActivate($hash)
    {
        try {
            $this->newsletterValidator->validateActivate($hash);

            $bool = $this->newsletterModel->update(
                ['status' => Newsletter\Helper\Subscribe::ACCOUNT_STATUS_CONFIRMED],
                ['hash' => $hash],
                Newsletter\Model::TABLE_NAME_ACCOUNTS
            );

            $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->setContent($this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionIndex()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_indexPost($this->request->getPost()->getAll());
        }

        $defaults = [
            'first_name' => '',
            'last_name' => '',
            'mail' => ''
        ];
        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));

        $salutationsLang = [
            $this->lang->t('newsletter', 'salutation_female'),
            $this->lang->t('newsletter', 'salutation_male')
        ];
        $this->view->assign('salutation', $this->get('core.helpers.forms')->selectGenerator('salutation', [1, 2], $salutationsLang));

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha());
        }

        $this->formTokenHelper->generateFormToken();
    }

    public function actionUnsubscribe()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_unsubscribePost($this->request->getPost()->getAll());
        }

        $defaults = [
            'mail' => ''
        ];
        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     */
    protected function _indexPost(array $formData)
    {
        $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->newsletterValidator->validateSubscribe($formData);

                $bool = $this->subscribeHelper->subscribeToNewsletter(
                    $formData['mail'],
                    $formData['salutation'],
                    $formData['first_name'],
                    $formData['last_name']
                );

                $this->formTokenHelper->unsetFormToken();

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
            }
        );
    }

    /**
     * @param array $formData
     */
    protected function _unsubscribePost(array $formData)
    {
        $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->newsletterValidator->validateUnsubscribe($formData);

                $bool = $this->newsletterModel->update(
                    ['status' => Newsletter\Helper\Subscribe::ACCOUNT_STATUS_DISABLED],
                    ['mail' => $formData['mail']],
                    Newsletter\Model::TABLE_NAME_ACCOUNTS
                );

                $this->formTokenHelper->unsetFormToken();

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->lang->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
            }
        );
    }
}
