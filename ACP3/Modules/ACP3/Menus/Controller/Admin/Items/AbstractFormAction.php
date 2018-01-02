<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @param array $formData
     *
     * @return string
     */
    protected function fetchMenuItemModeForSave(array $formData)
    {
        return ($formData['mode'] == 2 || $formData['mode'] == 3) && preg_match(
            Menus\Helpers\MenuItemsList::ARTICLES_URL_KEY_REGEX,
            $formData['uri']
        ) ? '4' : $formData['mode'];
    }

    /**
     * @param array $formData
     *
     * @return string
     */
    protected function fetchMenuItemUriForSave(array $formData)
    {
        return $formData['mode'] == 1 ? $formData['module'] : ($formData['mode'] == 4 ? sprintf(
            Articles\Helpers::URL_KEY_PATTERN,
            $formData['articles']
        ) : $formData['uri']);
    }
}
