<?php

namespace ACP3\Modules\Search;

use ACP3\Core;

/**
 * Description of SearchFrontend
 *
 * @author Tino Goratsch
 */
abstract class Helpers
{

    /**
     * Gibt die für die Suche verfügbaren Module zurück
     *
     * @return array
     */
    public static function getModules()
    {
        $className = "\\ACP3\\Modules\\Search\\Extensions";
        $modules = get_class_methods($className);
        $searchModules = array();

        foreach ($modules as $module) {
            $module = substr($module, 0, strpos($module, 'Search'));
            if (Core\Modules::hasPermission('frontend/' . $module) === true) {
                $info = Core\Modules::getModuleInfo($module);
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