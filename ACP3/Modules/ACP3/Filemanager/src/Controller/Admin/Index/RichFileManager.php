<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use RFM\Application;

class RichFileManager extends AbstractWidgetAction
{
    public function __construct(WidgetContext $context, private Application $rfmApp)
    {
        parent::__construct($context);
    }

    /**
     * @throws \Exception
     */
    public function __invoke(): void
    {
        $this->rfmApp->run();
    }
}
