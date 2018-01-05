<?php
namespace ACP3\Core\Modules\Helper;

use Psr\Container\ContainerInterface;

class ControllerActionExists
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ControllerActionExists constructor.
     * @param ContainerInterface $container
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
        $pathArray = explode('/', strtolower($path));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $serviceId = $pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2] . '.' . $pathArray[3];

        return $this->container->has($serviceId);
    }
}
