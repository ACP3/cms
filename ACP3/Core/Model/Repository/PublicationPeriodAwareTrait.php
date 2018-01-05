<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

trait PublicationPeriodAwareTrait
{
    /**
     * @param string $tableAlias
     *
     * @return string
     */
    protected function getPublicationPeriod($tableAlias = '')
    {
        return sprintf(
            '(%1$sstart = %1$send AND %1$sstart <= :time OR %1$sstart != %1$send AND :time BETWEEN %1$sstart AND %1$send)',
            $tableAlias
        );
    }
}
