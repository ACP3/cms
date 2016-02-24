<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface ActionInterface
 * @package ACP3\Core\Controller
 */
interface ActionInterface
{
    public function preDispatch();

    /**
     * Gets a class from the service container
     *
     * @param string $serviceId
     *
     * @return mixed
     */
    public function get($serviceId);

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container);

    /**
     * @param mixed $actionResult
     */
    public function display($actionResult);
}