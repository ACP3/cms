<?php

namespace ACP3\Installer\Modules\Update;

use ACP3\Core;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class Helpers
 * @package ACP3\Installer\Modules\Update
 */
class Helpers
{
    /**
     * @var Core\Modules
     */
    protected $modules;

    public function __construct(Core\Modules $modules)
    {
        $this->modules = $modules;
    }

    /**
     * Führt die Updateanweisungen eines Moduls aus
     *
     * @param string                                           $module
     * @param \Symfony\Component\DependencyInjection\Container $container
     *
     * @return integer
     */
    public function updateModule($module, Container $container)
    {
        $result = false;

        $serviceId = $module . '.installer';
        if ($container->has($serviceId) === true) {
            /** @var Core\Modules\AbstractInstaller $installer */
            $installer = $container->get($serviceId);
            if ($installer instanceof Core\Modules\AbstractInstaller &&
                ($this->modules->isInstalled($module) || count($installer->renameModule()) > 0)
            ) {
                $result = $installer->updateSchema();
            }
        }

        return $result;
    }

}