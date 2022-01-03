<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

class AbstractWidgetActionImpl extends AbstractWidgetAction
{
    protected function applyTemplateAutomatically(): string
    {
        return 'Foo/Frontend/index.index.tpl';
    }
}
