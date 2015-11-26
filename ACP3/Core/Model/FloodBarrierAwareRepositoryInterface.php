<?php
namespace ACP3\Core\Model;

/**
 * Class FloodBarrierAwareRepositoryInterface
 * @package ACP3\Core\Model
 */
interface FloodBarrierAwareRepositoryInterface
{
    /**
     * @param $ipAddress
     *
     * @return mixed
     */
    public function getLastDateFromIp($ipAddress);
}