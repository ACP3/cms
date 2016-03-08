<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AdminAction;
use ACP3\Core\Controller\Context\AdminContext;
use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
abstract class AbstractFormAction extends AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Helpers
     */
    protected $articlesHelpers;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * AbstractFormAction constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms                   $formsHelper
     */
    public function __construct(AdminContext $context, Forms $formsHelper)
    {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Articles\Helpers $articlesHelpers
     *
     * @return $this
     */
    public function setArticlesHelpers(Articles\Helpers $articlesHelpers)
    {
        $this->articlesHelpers = $articlesHelpers;

        return $this;
    }

    /**
     * @param array $formData
     *
     * @return string
     */
    protected function fetchMenuItemModeForSave(array $formData)
    {
        return ($formData['mode'] == 2 || $formData['mode'] == 3) && preg_match(Menus\Helpers\MenuItemsList::ARTICLES_URL_KEY_REGEX,
            $formData['uri']) ? '4' : $formData['mode'];
    }

    /**
     * @param array $formData
     *
     * @return string
     */
    protected function fetchMenuItemUriForSave(array $formData)
    {
        return $formData['mode'] == 1 ? $formData['module'] : ($formData['mode'] == 4 ? sprintf(Articles\Helpers::URL_KEY_PATTERN,
            $formData['articles']) : $formData['uri']);
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function fetchMenuItemTypes($value = '')
    {
        $menuItemTypes = [
            1 => $this->translator->t('menus', 'module'),
            2 => $this->translator->t('menus', 'dynamic_page'),
            3 => $this->translator->t('menus', 'hyperlink')
        ];
        if ($this->articlesHelpers) {
            $menuItemTypes[4] = $this->translator->t('menus', 'article');
        }

        return $this->formsHelper->choicesGenerator('mode', $menuItemTypes, $value);
    }

    /**
     * @param array $menuItem
     *
     * @return array
     */
    protected function fetchModules(array $menuItem = [])
    {
        $modules = $this->modules->getAllModules();
        foreach ($modules as $row) {
            $row['dir'] = strtolower($row['dir']);
            $modules[$row['name']]['selected'] = $this->formsHelper->selectEntry(
                'module',
                $row['dir'],
                !empty($menuItem) && $menuItem['mode'] == 1 ? $menuItem['uri'] : ''
            );
        }
        return $modules;
    }
}
