<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Title extends \ACP3\Core\Breadcrumb\Title
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var array
     */
    private $systemSettings = [];

    /**
     * Title constructor.
     *
     * @param RequestInterface         $request
     * @param Steps                    $steps
     * @param EventDispatcherInterface $eventDispatcher
     * @param SettingsInterface        $settings
     */
    public function __construct(
        RequestInterface $request,
        Steps $steps,
        EventDispatcherInterface $eventDispatcher,
        SettingsInterface $settings
    ) {
        parent::__construct($steps, $eventDispatcher);

        $this->settings = $settings;
        $this->request = $request;
    }

    /**
     * @return array
     */
    private function getSettings()
    {
        if (empty($this->systemSettings)) {
            $this->systemSettings = $this->settings->getSettings(Schema::MODULE_NAME);
        }

        return $this->systemSettings;
    }

    public function getSiteAndPageTitle()
    {
        $settings = $this->getSettings();

        if ($this->allowSystemSubtitle()) {
            if ($this->request->isHomepage()) {
                if ($settings['site_subtitle_homepage_mode'] == 1) {
                    $title = $this->getSiteSubtitle();
                    $title .= $this->getSiteTitleSeparator() . $this->getSiteTitle();

                    return $title;
                }
            } elseif ($settings['site_subtitle_mode'] == 2) {
                $this->setSiteSubtitle('');
            }
        }

        return parent::getSiteAndPageTitle();
    }

    private function allowSystemSubtitle(): bool
    {
        return $this->getSettings()['site_subtitle_mode'] != 3;
    }

    protected function renderSiteSubTitle(): string
    {
        if ($this->allowSystemSubtitle()) {
            return parent::renderSiteSubTitle();
        }

        return '';
    }
}
