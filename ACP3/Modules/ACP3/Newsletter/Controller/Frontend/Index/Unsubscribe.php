<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Unsubscribe
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index
 */
class Unsubscribe extends Core\Modules\FrontendController
{
    use Newsletter\Controller\CaptchaHelperTrait;

    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus
     */
    protected $accountStatusHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\UnsubscribeFormValidation
     */
    protected $unsubscribeFormValidation;

    /**
     * Unsubscribe constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext                      $context
     * @param \ACP3\Core\Helpers\FormToken                                       $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus                 $accountStatusHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\UnsubscribeFormValidation $unsubscribeFormValidation
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Helper\AccountStatus $accountStatusHelper,
        Newsletter\Validation\UnsubscribeFormValidation $unsubscribeFormValidation
    )
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->accountStatusHelper = $accountStatusHelper;
        $this->unsubscribeFormValidation = $unsubscribeFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        $defaults = [
            'mail' => ''
        ];

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->captchaHelpers->captcha());
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'form' => array_merge($defaults, $this->request->getPost()->all())
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->unsubscribeFormValidation->validate($formData);

                $bool = $this->accountStatusHelper->changeAccountStatus(
                    Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_DISABLED,
                    ['mail' => $formData['mail']]
                );

                $this->formTokenHelper->unsetFormToken();

                $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->translator->t('newsletter',
                    $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), $this->appPath->getWebRoot()));
            }
        );
    }
}
