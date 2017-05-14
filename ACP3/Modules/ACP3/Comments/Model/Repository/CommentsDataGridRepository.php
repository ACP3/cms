<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model\Repository;


use ACP3\Core\Model\Repository\DataGridRepository;

class CommentsDataGridRepository extends DataGridRepository
{
    const TABLE_NAME = CommentRepository::TABLE_NAME;
}
