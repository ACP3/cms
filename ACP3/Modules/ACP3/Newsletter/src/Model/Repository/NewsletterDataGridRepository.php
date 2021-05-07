<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model\Repository;

use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;

class NewsletterDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = NewsletterRepository::TABLE_NAME;
}
