<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model;


use ACP3\Core\Model\DataGridRepository;

/**
 * Class NewsletterDataGridRepository
 * @package ACP3\Modules\ACP3\Newsletter\Model
 */
class NewsletterDataGridRepository extends DataGridRepository
{
    const TABLE_NAME = NewsletterRepository::TABLE_NAME;
}
