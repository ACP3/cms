<?php
namespace ACP3\Core\Model;

/**
 * Interface FloodBarrierAwareRepositoryInterface
 * @package ACP3\Core\Model
 */
interface FloodBarrierAwareRepositoryInterface
{
    /**
     * @param string $ipAddress
     *
     * @return mixed
     */
    public function getLastDateFromIp($ipAddress);
}
