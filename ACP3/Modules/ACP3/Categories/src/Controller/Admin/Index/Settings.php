<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Settings extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\ViewProviders\AdminCategorySettingsViewProvider
     */
    private $adminCategorySettingsViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Categories\ViewProviders\AdminCategorySettingsViewProvider $adminCategorySettingsViewProvider
    ) {
        parent::__construct($context);

        $this->adminCategorySettingsViewProvider = $adminCategorySettingsViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->adminCategorySettingsViewProvider)();
    }
}
