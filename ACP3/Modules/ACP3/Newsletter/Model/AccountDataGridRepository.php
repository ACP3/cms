<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model;


use ACP3\Core\Model\DataGridRepository;
use ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus;

/**
 * Class AccountDataGridRepository
 * @package ACP3\Modules\ACP3\Newsletter\Model
 */
class AccountDataGridRepository extends DataGridRepository
{
    const TABLE_NAME = AccountRepository::TABLE_NAME;

    /**
     * @return string
     */
    protected function getFrom()
    {
        return "{$this->getTableName()} WHERE `status` != :status";
    }

    /**
     * @return array
     */
    protected function getParameters()
    {
        return ['status' => AccountStatus::ACCOUNT_STATUS_DISABLED];
    }
}
