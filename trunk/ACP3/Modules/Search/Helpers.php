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
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * @param Core\ACL $acl
     * @param Core\Modules $modules
     * @param Core\Helpers\Forms $formsHelper
     */
    public function __construct(
        Core\ACL $acl,
        Core\Modules $modules,
        Core\Helpers\Forms $formsHelper
    )
    {
        $this->acl = $acl;
        $this->modules = $modules;
        $this->formsHelper = $formsHelper;
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
        $searchModules = [];

        foreach ($modules as $module) {
            $module = substr($module, 0, strpos($module, 'Search'));
            if ($this->acl->hasPermission('frontend/' . $module) === true) {
                $info = $this->modules->getModuleInfo($module);
                $name = $info['name'];
                $searchModules[$name]['dir'] = $module;
                $searchModules[$name]['checked'] = $this->formsHelper->selectEntry('mods', $module, $module, 'checked');
                $searchModules[$name]['name'] = $name;
            }
        }
        ksort($searchModules);

        return $searchModules;
    }

}