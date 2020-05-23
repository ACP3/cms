<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        FrontendContext $context,
        Modules $modules,
        Forms $formsHelper)
    {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->modules = $modules;
    }

    protected function fetchMenuItemModeForSave(array $formData): string
    {
        return ($formData['mode'] == 2 || $formData['mode'] == 3) && \preg_match(
            Menus\Helpers\MenuItemsList::ARTICLES_URL_KEY_REGEX,
            $formData['uri']
        ) ? '4' : $formData['mode'];
    }

    protected function fetchMenuItemUriForSave(array $formData): string
    {
        return $formData['mode'] == 1 ? $formData['module'] : ($formData['mode'] == 4 ? \sprintf(
            Articles\Helpers::URL_KEY_PATTERN,
            $formData['articles']
        ) : $formData['uri']);
    }

    protected function fetchMenuItemTypes(string $value = ''): array
    {
        $menuItemTypes = [
            1 => $this->translator->t('menus', 'module'),
            2 => $this->translator->t('menus', 'dynamic_page'),
            3 => $this->translator->t('menus', 'hyperlink'),
        ];

        return $this->formsHelper->choicesGenerator('mode', $menuItemTypes, $value);
    }

    protected function fetchModules(array $menuItem = []): array
    {
        $modules = $this->modules->getAllModulesAlphabeticallySorted();
        foreach ($modules as $row) {
            $modules[$row['name']]['selected'] = $this->formsHelper->selectEntry(
                'module',
                $row['name'],
                !empty($menuItem) && $menuItem['mode'] == 1 ? $menuItem['uri'] : ''
            );
        }

        return $modules;
    }
}
