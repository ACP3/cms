<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Activate extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus
     */
    protected $accountStatusHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\ActivateAccountFormValidation
     */
    protected $activateAccountFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alertsHelper;

    /**
     * Activate constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                          $context
     * @param \ACP3\Core\Helpers\Alerts                                              $alertsHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus                     $accountStatusHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\ActivateAccountFormValidation $activateAccountFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Alerts $alertsHelper,
        Newsletter\Helper\AccountStatus $accountStatusHelper,
        Newsletter\Validation\ActivateAccountFormValidation $activateAccountFormValidation
    ) {
        parent::__construct($context);

        $this->accountStatusHelper = $accountStatusHelper;
        $this->activateAccountFormValidation = $activateAccountFormValidation;
        $this->alertsHelper = $alertsHelper;
    }

    /**
     * @param string $hash
     */
    public function execute($hash)
    {
        try {
            $this->activateAccountFormValidation->validate(['hash' => $hash]);

            $bool = $this->accountStatusHelper->changeAccountStatus(
                Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_CONFIRMED,
                ['hash' => $hash]
            );

            $this->setTemplate($this->alertsHelper->confirmBox($this->translator->t(
                'newsletter',
                $bool !== false ? 'activate_success' : 'activate_error'
            ), $this->appPath->getWebRoot()));
        } catch (Core\Validation\Exceptions\ValidationFailedException $e) {
            $this->setContent($this->alertsHelper->errorBox($e->getMessage()));
        }
    }
}
