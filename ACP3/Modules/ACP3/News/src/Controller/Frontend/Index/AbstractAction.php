<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\News\Installer\Schema;

class AbstractAction extends AbstractFrontendAction
{
    /**
     * @var array
     */
    protected $newsSettings = [];
    /**
     * @var bool
     */
    protected $commentsActive = false;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->newsSettings = $this->config->getSettings(Schema::MODULE_NAME);
        $this->commentsActive = ($this->newsSettings['comments'] == 1 && $this->acl->hasPermission('frontend/comments') === true);
    }
}
