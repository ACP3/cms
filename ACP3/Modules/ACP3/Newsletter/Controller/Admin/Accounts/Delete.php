<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Accounts;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin\Accounts
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus
     */
    protected $accountStatusHelper;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext         $context
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus $accountStatusHelper
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Newsletter\Helper\AccountStatus $accountStatusHelper)
    {
        parent::__construct($context);

        $this->accountStatusHelper = $accountStatusHelper;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
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
