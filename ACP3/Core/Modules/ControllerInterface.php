<?php
namespace ACP3\Core\Modules;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface ControllerInterface
 * @package ACP3\Core\Modules
 */
interface ControllerInterface
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
     * @param mixed $controllerActionResult
     */
    public function display($controllerActionResult);
}