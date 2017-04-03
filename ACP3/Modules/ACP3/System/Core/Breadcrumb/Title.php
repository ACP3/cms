<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Title
 * @package ACP3\Modules\ACP3\System\Core\Breadcrumb
 */
class Title extends \ACP3\Core\Breadcrumb\Title
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * Title constructor.
     * @param Steps $steps
     * @param EventDispatcherInterface $eventDispatcher
     * @param SettingsInterface $settings
     */
    public function __construct(Steps $steps, EventDispatcherInterface $eventDispatcher, SettingsInterface $settings)
    {
        parent::__construct($steps, $eventDispatcher);

        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     */
    public function getSiteTitle()
    {
        if (empty(parent::getSiteTitle())) {
            $this->addSiteTitle();
        }

        return parent::getSiteTitle();
    }

    private function addSiteTitle()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if (isset($settings['site_title'])) {
            $this->setSiteTitle($settings['site_title']);
        }
    }

    /**
     * @inheritdoc
     */
    public function getSiteSubtitle()
    {
        if (empty(parent::getSiteSubtitle())) {
            $this->addSiteSubtitle();
        }

        return parent::getSiteSubtitle();
    }

    private function addSiteSubtitle()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if (!empty($settings['site_subtitle'])) {
            $this->setSiteSubtitle($settings['site_subtitle']);
        }
    }
}
