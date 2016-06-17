<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;
use ACP3\Modules\ACP3\Seo\Helper\UriAliasManager;

/**
 * Class AbstractFormAction
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
abstract class AbstractFormAction extends AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem
     */
    protected $manageMenuItemHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\UriAliasManager
     */
    protected $uriAliasManager;

    /**
     * @param \ACP3\Modules\ACP3\Menus\Cache $menusCache
     *
     * @return $this
     */
    public function setMenusCache(Menus\Cache $menusCache)
    {
        $this->menusCache = $menusCache;

        return $this;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem $manageMenuItemHelper
     *
     * @return $this
     */
    public function setManageMenuItemHelper(Menus\Helpers\ManageMenuItem $manageMenuItemHelper)
    {
        $this->manageMenuItemHelper = $manageMenuItemHelper;

        return $this;
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
     * @param int   $id
     */
    protected function createOrUpdateMenuItem(array $formData, $id)
    {
        if ($this->menusCache) {
            if ($this->acl->hasPermission('admin/menus/items/create') === true) {
                $data = [
                    'mode' => 4,
                    'block_id' => $formData['block_id'],
                    'parent_id' => (int)$formData['parent_id'],
                    'display' => $formData['display'],
                    'title' => $formData['title'],
                    'target' => 1
                ];

                $this->manageMenuItemHelper->manageMenuItem(
                    sprintf(Articles\Helpers::URL_KEY_PATTERN, $id),
                    isset($formData['create']) === true,
                    $data
                );
            }

            // Refresh the menu items cache
            $this->menusCache->saveMenusCache();
        }
    }

    /**
     * @param array $formData
     * @param int   $articleId
     */
    protected function insertUriAlias(array $formData, $articleId)
    {
        if ($this->uriAliasManager) {
            $this->uriAliasManager->insertUriAlias(
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $articleId),
                $formData['alias'],
                $formData['seo_keywords'],
                $formData['seo_description'],
                (int)$formData['seo_robots']
            );
        }
    }
}
