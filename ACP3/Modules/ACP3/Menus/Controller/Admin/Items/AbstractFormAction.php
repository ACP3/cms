<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AdminAction;
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
     * @return string
     */
    protected function fetchMenuItemModes($value = '')
    {
        $valuesMode = [1, 2, 3];
        $langMode = [
            $this->translator->t('menus', 'module'),
            $this->translator->t('menus', 'dynamic_page'),
            $this->translator->t('menus', 'hyperlink')
        ];
        if ($this->articlesHelpers) {
            $valuesMode[] = 4;
            $langMode[] = $this->translator->t('menus', 'article');
        }

        return $this->get('core.helpers.forms')->selectGenerator('mode', $valuesMode, $langMode, $value);
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
            $modules[$row['name']]['selected'] = $this->get('core.helpers.forms')->selectEntry(
                'module',
                $row['dir'],
                !empty($menuItem) && $menuItem['mode'] == 1 ? $menuItem['uri'] : ''
            );
        }
        return $modules;
    }
}