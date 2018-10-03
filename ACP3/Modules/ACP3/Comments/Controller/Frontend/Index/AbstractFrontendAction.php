<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Frontend\Index;

use ACP3\Core\Controller\AbstractFrontendAction as CoreAbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\Comments\Installer\Schema;
use ACP3\Modules\ACP3\Emoticons;

abstract class AbstractFrontendAction extends CoreAbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;
    /**
     * @var bool
     */
    protected $emoticonsActive = false;
    /**
     * @var array
     */
    protected $commentsSettings = [];

    public function __construct(FrontendContext $context, ?Emoticons\Helpers $emoticonsHelpers = null)
    {
        parent::__construct($context);

        $this->emoticonsHelpers = $emoticonsHelpers;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->commentsSettings = $this->config->getSettings(Schema::MODULE_NAME);
        $this->emoticonsActive = ($this->commentsSettings['emoticons'] == 1);
    }
}
