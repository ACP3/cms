<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Accounts;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Delete extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus
     */
    protected $accountStatusHelper;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Newsletter\Helper\AccountStatus $accountStatusHelper
    ) {
        parent::__construct($context);

        $this->accountStatusHelper = $accountStatusHelper;
    }

    /**
     * @param string $action
     *
     * @return mixed
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(string $action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->accountStatusHelper->changeAccountStatus(
                        Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_DISABLED,
                        $item
                    );
                }

                return $bool;
            }
        );
    }
}
