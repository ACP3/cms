<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model;

/**
 * Class AbstractModel
 * @package ACP3\Core\Model
 */
abstract class AbstractModel
{
    /**
     * @param AbstractRepository $repository
     * @param array $data
     * @param null $entryId
     * @return mixed
     */
    protected function save(AbstractRepository $repository, array $data, $entryId = null)
    {
        if (intval($entryId)) {
            return $repository->update($data, $entryId);
        }

        return $repository->insert($data);
    }
}
