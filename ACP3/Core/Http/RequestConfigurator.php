<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Http;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class RequestConfigurator
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * RequestConfigurator constructor.
     * @param RequestInterface $request
     * @param SettingsInterface $settings
     */
    public function __construct(RequestInterface $request, SettingsInterface $settings)
    {
        $this->request = $request;
        $this->settings = $settings;
    }

    /**
     * Configures the request
     */
    public function configure()
    {
        $this->request->setHomepage($this->settings->getSettings(Schema::MODULE_NAME)['homepage']);
        $this->request->processQuery();
    }
}
