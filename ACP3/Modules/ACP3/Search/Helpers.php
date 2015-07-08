<?php

namespace ACP3\Modules\ACP3\Search;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Search
 */
class Helpers
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Search\Extensions
     */
    protected $searchExtensions;

    /**
     * @param \ACP3\Core\ACL                       $acl
     * @param \ACP3\Core\Modules                   $modules
     * @param \ACP3\Core\Helpers\Forms             $formsHelper
     * @param \ACP3\Modules\ACP3\Search\Extensions $searchExtensions
     */
    public function __construct(
        Core\ACL $acl,
        Core\Modules $modules,
        Core\Helpers\Forms $formsHelper,
        Extensions $searchExtensions
    )
    {
        $this->acl = $acl;
        $this->modules = $modules;
        $this->formsHelper = $formsHelper;
        $this->searchExtensions = $searchExtensions;
    }

    /**
     * Gibt die für die Suche verfügbaren Module zurück
     *
     * @return array
     */
    public function getModules()
    {
        $modules = get_class_methods($this->searchExtensions);
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
