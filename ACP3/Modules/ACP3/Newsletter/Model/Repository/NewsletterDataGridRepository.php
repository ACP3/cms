<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model\Repository;

use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;

/**
 * Class NewsletterDataGridRepository
 * @package ACP3\Modules\ACP3\Newsletter\Model\Repository
 */
class NewsletterDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = NewsletterRepository::TABLE_NAME;
}
