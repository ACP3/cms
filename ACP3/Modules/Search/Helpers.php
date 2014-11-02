<?php

namespace ACP3\Modules\Search;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\Search
 */
class Helpers
{
    /**
     * @var Core\ACL
     */
    protected $acl;
    /**
     * @var Core\Modules
     */
    protected $modules;

    public function __construct(
        Core\ACL $acl,
        Core\Modules $modules
    )
    {
        $this->acl = $acl;
        $this->modules = $modules;
    }

    /**
     * Gibt die für die Suche verfügbaren Module zurück
     *
     * @return array
     */
    public function getModules()
    {
        $className = "\\ACP3\\Modules\\Search\\Extensions";
        $modules = get_class_methods($className);
        $searchModules = array();

        foreach ($modules as $module) {
            $module = substr($module, 0, strpos($module, 'Search'));
            if ($this->acl->hasPermission('frontend/' . $module) === true) {
                $info = $this->modules->getModuleInfo($module);
                $name = $info['name'];
                $searchModules[$name]['dir'] = $module;
                $searchModules[$name]['checked'] = Core\Functions::selectEntry('mods', $module, $module, 'checked');
                $searchModules[$name]['name'] = $name;
            }
        }
        ksort($searchModules);

        return $searchModules;
    }

}