<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model\Repository;

use ACP3\Core;

/**
 * Class AccountHistoryRepository
 * @package ACP3\Modules\ACP3\Newsletter\Model\Repository
 */
class AccountHistoryRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'newsletter_account_history';
}
