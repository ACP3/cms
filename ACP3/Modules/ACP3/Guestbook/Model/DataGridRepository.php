<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Model;

/**
 * Class DataGridRepository
 * @package ACP3\Modules\ACP3\Guestbook\Model
 */
class DataGridRepository extends \ACP3\Core\Model\DataGridRepository
{
    const TABLE_NAME = GuestbookRepository::TABLE_NAME;
}
