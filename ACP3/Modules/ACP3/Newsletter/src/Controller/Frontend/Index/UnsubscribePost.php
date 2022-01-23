<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Newsletter;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class UnsubscribePost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private ApplicationPath $applicationPath,
        private FormAction $actionHelper,
        private Core\Helpers\Alerts $alertsHelper,
        private Newsletter\Helper\AccountStatus $accountStatusHelper,
        private Newsletter\Validation\UnsubscribeFormValidation $unsubscribeFormValidation
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|JsonResponse|RedirectResponse|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(): array|string|JsonResponse|RedirectResponse|Response
    {
        return $this->actionHelper->handlePostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->unsubscribeFormValidation->validate($formData);

                $result = $this->accountStatusHelper->changeAccountStatus(
                    Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_DISABLED,
                    ['mail' => $formData['mail']]
                );

                return $this->alertsHelper->confirmBox(
                    $this->translator->t('newsletter', $result ? 'unsubscribe_success' : 'unsubscribe_error'),
                    $this->applicationPath->getWebRoot()
                );
            }
        );
    }
}
