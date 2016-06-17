<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Core\Controller\Context\AdminContext;
use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;
use ACP3\Modules\ACP3\Seo\Helper\MetaFormFields;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
abstract class AbstractFormAction extends AbstractAdminAction
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
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields
     */
    protected $metaFormFieldsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatementsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

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
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatementsHelper
     */
    public function setMetaStatementsHelper(MetaStatements $metaStatementsHelper)
    {
        $this->metaStatementsHelper = $metaStatementsHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaFormFields $metaFormFieldsHelper
     */
    public function setMetaFormFieldsHelper(MetaFormFields $metaFormFieldsHelper)
    {
        $this->metaFormFieldsHelper = $metaFormFieldsHelper;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager $uriAliasManager
     */
    public function setUriAliasManager(UriAliasManager $uriAliasManager)
    {
        $this->uriAliasManager = $uriAliasManager;
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

    /**
     * @param array  $formData
     * @param string $path
     */
    protected function insertUriAlias(array $formData, $path)
    {
        if ($this->uriAliasManager) {
            $this->uriAliasManager->insertUriAlias(
                $path,
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
        }
    }
}
