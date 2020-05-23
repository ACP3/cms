<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

abstract class AbstractFormAction extends AbstractFrontendAction
{
    protected function fetchMenuItemModeForSave(array $formData): string
    {
        return ($formData['mode'] == 2 || $formData['mode'] == 3) && \preg_match(
            Menus\Helpers\MenuItemsList::ARTICLES_URL_KEY_REGEX,
            $formData['uri']
        ) ? '4' : $formData['mode'];
    }

    protected function fetchMenuItemUriForSave(array $formData): string
    {
        switch ((int) $formData['mode']) {
            case 1:
                return $formData['module'];
            case 4:
                return \sprintf(
                    Articles\Helpers::URL_KEY_PATTERN,
                    $formData['articles']
                );
            default:
                return $formData['uri'];
        }
    }
}
