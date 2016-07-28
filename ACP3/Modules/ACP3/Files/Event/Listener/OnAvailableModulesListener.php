<?php
namespace ACP3\Modules\ACP3\Files\Event\Listener;

use ACP3\Modules\ACP3\Search\Event\AvailableModulesEvent;

/**
 * Class OnAvailableModulesListener
 * @package ACP3\Modules\ACP3\Files\Event\Listener
 */
class OnAvailableModulesListener
{
    /**
     * @param \ACP3\Modules\ACP3\Search\Event\AvailableModulesEvent $availableModules
     */
    public function onAvailableModules(AvailableModulesEvent $availableModules)
    {
        $availableModules->addAvailableModule('files');
    }
}
