<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Comments\ViewProviders\AdminCommentsSettingsViewProvider;

class Settings extends AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\ViewProviders\AdminCommentsSettingsViewProvider
     */
    private $adminCommentsSettingsViewProvider;

    public function __construct(
        WidgetContext $context,
        AdminCommentsSettingsViewProvider $adminCommentsSettingsViewProvider
    ) {
        parent::__construct($context);

        $this->adminCommentsSettingsViewProvider = $adminCommentsSettingsViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->adminCommentsSettingsViewProvider)();
    }
}
