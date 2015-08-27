<?php

namespace ACP3\Modules\ACP3\Search;

use ACP3\Core;
use ACP3\Modules\ACP3\Search\Event\AvailableModules;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param \ACP3\Core\ACL                                     $acl
     * @param \ACP3\Core\Modules                                 $modules
     * @param \ACP3\Core\Helpers\Forms                           $formsHelper
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     */
    public function __construct(
        Core\ACL $acl,
        Core\Modules $modules,
        Core\Helpers\Forms $formsHelper,
        EventDispatcher $eventDispatcher
    )
    {
        $this->acl = $acl;
        $this->modules = $modules;
        $this->formsHelper = $formsHelper;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gibt die für die Suche verfügbaren Module zurück
     *
     * @return array
     */
    public function getModules()
    {
        $availableModules = new AvailableModules();
        $this->eventDispatcher->dispatch('search.events.availableModules', $availableModules);
        $searchModules = [];

        foreach ($availableModules->getAvailableModules() as $module) {
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
