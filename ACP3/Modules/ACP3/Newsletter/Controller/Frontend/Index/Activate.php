<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
     * Activate constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                          $context
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus                     $accountStatusHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\ActivateAccountFormValidation $activateAccountFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Newsletter\Helper\AccountStatus $accountStatusHelper,
        Newsletter\Validation\ActivateAccountFormValidation $activateAccountFormValidation
    ) {
        parent::__construct($context);

        $this->accountStatusHelper = $accountStatusHelper;
        $this->activateAccountFormValidation = $activateAccountFormValidation;
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

            $this->setTemplate($this->get('core.helpers.alerts')->confirmBox($this->translator->t('newsletter',
                $bool !== false ? 'activate_success' : 'activate_error'), $this->appPath->getWebRoot()));
        } catch (Core\Validation\Exceptions\ValidationFailedException $e) {
            $this->setContent($this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
