<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Activate
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index
 */
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
     * @var Core\Helpers\Alerts
     */
    private $alerts;

    /**
     * Activate constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\Helpers\Alerts $alerts
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus $accountStatusHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\ActivateAccountFormValidation $activateAccountFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Alerts $alerts,
        Newsletter\Helper\AccountStatus $accountStatusHelper,
        Newsletter\Validation\ActivateAccountFormValidation $activateAccountFormValidation
    ) {
        parent::__construct($context);

        $this->accountStatusHelper = $accountStatusHelper;
        $this->activateAccountFormValidation = $activateAccountFormValidation;
        $this->alerts = $alerts;
    }

    /**
     * @param string $hash
     * @return string
     */
    public function execute($hash)
    {
        try {
            $this->activateAccountFormValidation->validate(['hash' => $hash]);

            $bool = $this->accountStatusHelper->changeAccountStatus(
                Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_CONFIRMED,
                ['hash' => $hash]
            );

            return $this->alerts->confirmBox(
                $this->translator->t(
                'newsletter',
                    $bool !== false
                    ? 'activate_success'
                    : 'activate_error'
            ),
                $this->appPath->getWebRoot()
            );
        } catch (Core\Validation\Exceptions\ValidationFailedException $e) {
            return $this->alerts->errorBox($e->getMessage());
        }
    }
}
