<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Unsubscribe extends Core\Controller\AbstractFrontendAction
{
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
     * @param \ACP3\Core\Controller\Context\FrontendContext                      $context
     * @param \ACP3\Core\Helpers\FormToken                                       $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus                 $accountStatusHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\UnsubscribeFormValidation $unsubscribeFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Helper\AccountStatus $accountStatusHelper,
        Newsletter\Validation\UnsubscribeFormValidation $unsubscribeFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->accountStatusHelper = $accountStatusHelper;
        $this->unsubscribeFormValidation = $unsubscribeFormValidation;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $defaults = [
            'mail' => '',
        ];

        return [
            'form' => \array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->unsubscribeFormValidation->validate($formData);

                $bool = $this->accountStatusHelper->changeAccountStatus(
                    Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_DISABLED,
                    ['mail' => $formData['mail']]
                );

                $this->setTemplate(
                    $this->get('core.helpers.alerts')->confirmBox(
                    $this->translator->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'),
                    $this->appPath->getWebRoot()
                )
                );
            }
        );
    }
}
