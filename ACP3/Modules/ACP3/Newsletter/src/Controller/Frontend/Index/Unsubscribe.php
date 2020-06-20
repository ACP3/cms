<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Newsletter;

class Unsubscribe extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus
     */
    private $accountStatusHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\UnsubscribeFormValidation
     */
    private $unsubscribeFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\NewsletterUnsubscribeViewProvider
     */
    private $newsletterUnsubscribeViewProvider;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Core\Helpers\Alerts $alertsHelper,
        Newsletter\Helper\AccountStatus $accountStatusHelper,
        Newsletter\Validation\UnsubscribeFormValidation $unsubscribeFormValidation,
        Newsletter\ViewProviders\NewsletterUnsubscribeViewProvider $newsletterUnsubscribeViewProvider
    ) {
        parent::__construct($context);

        $this->accountStatusHelper = $accountStatusHelper;
        $this->unsubscribeFormValidation = $unsubscribeFormValidation;
        $this->alertsHelper = $alertsHelper;
        $this->newsletterUnsubscribeViewProvider = $newsletterUnsubscribeViewProvider;
        $this->actionHelper = $actionHelper;
    }

    public function execute(): array
    {
        return ($this->newsletterUnsubscribeViewProvider)();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
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
                    $this->alertsHelper->confirmBox(
                        $this->translator->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'),
                        $this->appPath->getWebRoot()
                    )
                );
            }
        );
    }
}
