<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

class Settings extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\ViewProviders\AdminCommentsSettingsViewProvider
     */
    private $adminCommentsSettingsViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Comments\ViewProviders\AdminCommentsSettingsViewProvider $adminCommentsSettingsViewProvider
    ) {
        parent::__construct($context);

        $this->adminCommentsSettingsViewProvider = $adminCommentsSettingsViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->adminCommentsSettingsViewProvider)();
    }
}
