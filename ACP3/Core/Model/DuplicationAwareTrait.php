<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Model\Repository\AbstractRepository;

trait DuplicationAwareTrait
{
    /**
     * @param int $entryId
     *
     * @return bool|int
     */
    public function duplicate($entryId)
    {
        $resultSet = $this->getRepository()->getOneById($entryId);

        if (!empty($resultSet)) {
            return $this->save(\array_merge($resultSet, $this->getDefaultDataForDuplication()));
        }

        return false;
    }

    /**
     * @return AbstractRepository
     */
    abstract protected function getRepository();

    /**
     * @param array    $rawData
     * @param null|int $entryId
     *
     * @return int|bool
     */
    abstract public function save(array $rawData, $entryId = null);

    /**
     * @return array
     */
    protected function getDefaultDataForDuplication()
    {
        return [];
    }
}
