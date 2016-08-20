<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Repository;

/**
 * Interface FloodBarrierAwareRepositoryInterface
 * @package ACP3\Core\Model\Repository
 */
interface FloodBarrierAwareRepositoryInterface
{
    /**
     * @param string $ipAddress
     *
     * @return string
     */
    public function getLastDateFromIp($ipAddress);
}
