<?php
namespace ACP3\Core\Authentication;

/**
 * Interface AuthenticationInterface
 * @package ACP3\Core\Authentication
 */
interface AuthenticationInterface
{
    /**
     * @return int
     */
    public function authenticate();
}