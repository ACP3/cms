<?php
namespace ACP3\Core\Authentication;

/**
 * Interface AuthenticationInterface
 * @package ACP3\Core\Authentication
 */
interface AuthenticationInterface
{
    /**
     * @return array|int
     */
    public function authenticate();
}