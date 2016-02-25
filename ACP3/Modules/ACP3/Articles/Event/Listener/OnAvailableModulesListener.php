<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Event\Listener;

use ACP3\Modules\ACP3\Search\Event\AvailableModules;

/**
 * Class OnAvailableModulesListener
 * @package ACP3\Modules\ACP3\Articles\Event\Listener
 */
class OnAvailableModulesListener
{
    /**
     * @param \ACP3\Modules\ACP3\Search\Event\AvailableModules $availableModules
     */
    public function onAvailableModules(AvailableModules $availableModules)
    {
        $availableModules->addAvailableModule('articles');
    }
}
