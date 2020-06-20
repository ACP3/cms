<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Newsletter;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe
     */
    private $subscribeHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\SubscribeFormValidation
     */
    private $subscribeFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\NewsletterSubscribeViewProvider
     */
    private $newsletterSubscribeViewProvider;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Core\Helpers\Alerts $alertsHelper,
        Newsletter\Helper\Subscribe $subscribeHelper,
        Newsletter\Validation\SubscribeFormValidation $subscribeFormValidation,
        Newsletter\ViewProviders\NewsletterSubscribeViewProvider $newsletterSubscribeViewProvider
    ) {
        parent::__construct($context);

        $this->subscribeHelper = $subscribeHelper;
        $this->subscribeFormValidation = $subscribeFormValidation;
        $this->alertsHelper = $alertsHelper;
        $this->newsletterSubscribeViewProvider = $newsletterSubscribeViewProvider;
        $this->actionHelper = $actionHelper;
    }

    public function execute(): array
    {
        return ($this->newsletterSubscribeViewProvider)();
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

                $this->subscribeFormValidation->validate($formData);

                $bool = $this->subscribeHelper->subscribeToNewsletter(
                    $formData['mail'],
                    $formData['salutation'],
                    $formData['first_name'],
                    $formData['last_name']
                );

                $this->setTemplate(
                    $this->alertsHelper->confirmBox(
                        $this->translator->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'),
                        $this->appPath->getWebRoot()
                    )
                );
            }
        );
    }
}
