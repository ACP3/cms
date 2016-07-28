<?php
namespace ACP3\Modules\ACP3\Search\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class AvailableModulesEvent
 * @package ACP3\Modules\ACP3\Search\Event
 */
class AvailableModulesEvent extends Event
{
    /**
     * @var array
     */
    private $availableModules = [];

    /**
     * @return array
     */
    public function getAvailableModules()
    {
        return $this->availableModules;
    }

    /**
     * @param string $module
     */
    public function addAvailableModule($module)
    {
        $this->availableModules[] = (string)$module;
    }
}
