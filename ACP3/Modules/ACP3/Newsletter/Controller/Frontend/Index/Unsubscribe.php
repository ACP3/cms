<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Unsubscribe extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus
     */
    protected $accountStatusHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\UnsubscribeFormValidation
     */
    protected $unsubscribeFormValidation;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;
    /**
     * @var Core\Helpers\Alerts
     */
    private $alerts;

    /**
     * Unsubscribe constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param Core\Helpers\Alerts $alerts
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus $accountStatusHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\UnsubscribeFormValidation $unsubscribeFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Core\Helpers\Alerts $alerts,
        Newsletter\Helper\AccountStatus $accountStatusHelper,
        Newsletter\Validation\UnsubscribeFormValidation $unsubscribeFormValidation
    ) {
        parent::__construct($context);

        $this->accountStatusHelper = $accountStatusHelper;
        $this->unsubscribeFormValidation = $unsubscribeFormValidation;
        $this->block = $block;
        $this->alerts = $alerts;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
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

                return $this->alerts->confirmBox(
                    $this->translator->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'),
                    $this->appPath->getWebRoot()
                );
            }
        );
    }
}
