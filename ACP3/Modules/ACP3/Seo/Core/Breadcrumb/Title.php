<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Config;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Title
 * @package ACP3\Modules\ACP3\Seo\Core\Breadcrumb
 */
class Title extends \ACP3\Core\Breadcrumb\Title
{
    /**
     * Title constructor.
     *
     * @param \ACP3\Core\Breadcrumb\Steps                                 $steps
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \ACP3\Core\Config                                           $config
     */
    public function __construct(Steps $steps, EventDispatcherInterface $eventDispatcher, Config $config)
    {
        parent::__construct($steps, $eventDispatcher);

        $this->siteTitle = $config->getSettings(Schema::MODULE_NAME)['title'];
    }
}
