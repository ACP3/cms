<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

interface ReaderRepositoryInterface
{
    /**
     * @param int $entryId
     * @return array
     */
    public function getOneById(int $entryId);
}
