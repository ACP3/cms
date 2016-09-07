<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Settings\SettingsInterface;
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
     * @param Steps $steps
     * @param EventDispatcherInterface $eventDispatcher
     * @param SettingsInterface $config
     */
    public function __construct(Steps $steps, EventDispatcherInterface $eventDispatcher, SettingsInterface $config)
    {
        parent::__construct($steps, $eventDispatcher);

        $settings = $config->getSettings(Schema::MODULE_NAME);
        if (isset($settings['title'])) {
            $this->siteTitle = $settings['title'];
        }
    }
}
