<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;

class Settings extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Feeds\ViewProviders\AdminSettingsViewProvider
     */
    private $adminSettingsViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Feeds\ViewProviders\AdminSettingsViewProvider $adminSettingsViewProvider
    ) {
        parent::__construct($context);

        $this->adminSettingsViewProvider = $adminSettingsViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->adminSettingsViewProvider)();
    }
}
