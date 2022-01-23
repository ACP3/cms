<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AbstractWidgetAction;

abstract class AbstractFormAction extends AbstractWidgetAction
{
    /**
     * @param array<string, mixed> $formData
     */
    protected function fetchMenuItemUriForSave(array $formData): string
    {
        if ((int) $formData['mode']) {
            return $formData['module'];
        }

        return $formData['uri'];
    }
}
