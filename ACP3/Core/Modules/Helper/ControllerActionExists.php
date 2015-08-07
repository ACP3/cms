<?php
namespace ACP3\Core\Modules\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ControllerActionExists
 * @package ACP3\Core\Modules\Helper
 */
class ControllerActionExists
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns, whether the given module controller action exists
     *
     * @param string $path
     *
     * @return boolean
     */
    public function controllerActionExists($path)
    {
        $pathArray = explode('/', strtolower(str_replace('_', '', $path)));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $serviceId = $pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2];

        if ($this->container->has($serviceId)) {
            return method_exists($this->container->get($serviceId), 'action' . $pathArray[3]);
        }

        return false;
    }
}