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
        $search_mods = array();

        foreach ($modules as $module) {
            $module = substr($module, 0, strpos($module, 'Search'));
            if (Core\Modules::hasPermission($module, 'list') === true) {
                $info = Core\Modules::getModuleInfo($module);
                $name = $info['name'];
                $search_mods[$name]['dir'] = $module;
                $search_mods[$name]['checked'] = Core\Functions::selectEntry('mods', $module, $module, 'checked');
                $search_mods[$name]['name'] = $name;
            }
        }
        ksort($search_mods);

        return $search_mods;
    }

}