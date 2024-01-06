<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use ACP3\Modules\ACP3\Filemanager\Helpers;

class Index extends AbstractWidgetAction
{
    public function __construct(Context $context, private readonly Helpers $fileManagerHelpers)
    {
        parent::__construct($context);
    }

    /**
     * @return array{filemanager_path: string}
     */
    public function __invoke(): array
    {
        return [
            'filemanager_path' => $this->fileManagerHelpers->getFilemanagerPath(),
        ];
    }
}
