<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\View\Block\Admin;

use ACP3\Core\Modules\Modules;
use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields;
use ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemsRepository;

class MenuItemAdminFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var MenuItemFormFields
     */
    private $menuItemFormFields;
    /**
     * @var Articles\Helpers
     */
    private $articlesHelpers;

    /**
     * MenuItemFormBlock constructor.
     * @param FormBlockContext $context
     * @param MenuItemsRepository $menuItemsRepository
     * @param Modules $modules
     * @param MenuItemFormFields $menuItemFormFields
     */
    public function __construct(
        FormBlockContext $context,
        MenuItemsRepository $menuItemsRepository,
        Modules $modules,
        MenuItemFormFields $menuItemFormFields
    ) {
        parent::__construct($context, $menuItemsRepository);

        $this->modules = $modules;
        $this->menuItemFormFields = $menuItemFormFields;
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
     * @inheritdoc
     */
    public function render()
    {
        $menuItem = $this->getData();

        $this->breadcrumb->setLastStepReplacement(
            $this->translator->t('menus', !$this->getId() ? 'admin_items_create' : 'admin_items_edit')
        );

        $this->title->setPageTitlePrefix($menuItem['title']);

        return \array_merge(
            $this->menuItemFormFields->createMenuItemFormFields(
                $menuItem['block_id'],
                $menuItem['parent_id'],
                $menuItem['left_id'],
                $menuItem['right_id'],
                $menuItem['display']
            ),
            $this->getArticles($menuItem),
            [
                'mode' => $this->fetchMenuItemTypes($menuItem['mode']),
                'modules' => $this->fetchModules($menuItem),
                'target' => $this->forms->linkTargetChoicesGenerator('target', $menuItem['target']),
                'form' => \array_merge($menuItem, $this->getRequestData()),
                'form_token' => $this->formToken->renderFormToken(),
            ]
        );
    }

    /**
     * @param string $value
     *
     * @return array
     */
    private function fetchMenuItemTypes(string $value = ''): array
    {
        $menuItemTypes = [
            1 => $this->translator->t('menus', 'module'),
            2 => $this->translator->t('menus', 'dynamic_page'),
            3 => $this->translator->t('menus', 'hyperlink'),
        ];
        if ($this->articlesHelpers) {
            $menuItemTypes[4] = $this->translator->t('menus', 'article');
        }

        return $this->forms->choicesGenerator('mode', $menuItemTypes, $value);
    }

    /**
     * @param array $menuItem
     *
     * @return array
     */
    private function fetchModules(array $menuItem = []): array
    {
        $modules = $this->modules->getAllModulesAlphabeticallySorted();
        foreach ($modules as $row) {
            $row['dir'] = \strtolower($row['dir']);
            $modules[$row['name']]['selected'] = $this->forms->selectEntry(
                'module',
                $row['dir'],
                !empty($menuItem) && $menuItem['mode'] == 1 ? $menuItem['uri'] : ''
            );
        }

        return $modules;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'title' => '',
            'mode' => 1,
            'uri' => '',
            'target' => 1,
            'block_id' => 0,
            'parent_id' => 0,
            'left_id' => 0,
            'right_id' => 0,
            'display' => 1,
        ];
    }

    /**
     * @param array $menuItem
     * @return array
     */
    private function getArticles(array $menuItem): array
    {
        $results = [];
        if ($this->articlesHelpers) {
            $matches = [];
            if (\count($this->getRequestData()) == 0 && $menuItem['mode'] == 4) {
                \preg_match_all(MenuItemsList::ARTICLES_URL_KEY_REGEX, $menuItem['uri'], $matches);
            }

            $results['articles'] = $this->articlesHelpers->articlesList(!empty($matches[2]) ? $matches[2][0] : '');
        }

        return $results;
    }
}
