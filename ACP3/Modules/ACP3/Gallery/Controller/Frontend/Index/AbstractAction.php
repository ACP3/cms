<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;

abstract class AbstractAction extends AbstractFrontendAction
{
    /**
     * @var array
     */
    protected $settings = [];

    public function preDispatch()
    {
        parent::preDispatch();

        $this->settings = $this->config->getSettings(Schema::MODULE_NAME);
    }
}
