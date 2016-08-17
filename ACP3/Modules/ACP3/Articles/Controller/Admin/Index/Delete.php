<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Articles\Controller\Admin\Index
 */
class Delete extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Articles\Cache
     */
    protected $articlesCache;
    /**
     * @var Articles\Model\ArticlesModel
     */
    protected $articlesModel;

    /**
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Core\Helpers\Forms $formsHelper
     * @param Articles\Model\ArticlesModel $articlesModel
     * @param \ACP3\Modules\ACP3\Articles\Cache $articlesCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Articles\Model\ArticlesModel $articlesModel,
        Articles\Cache $articlesCache
    ) {
        parent::__construct($context, $formsHelper);

        $this->articlesCache = $articlesCache;
        $this->articlesModel = $articlesModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                $bool = false;

                foreach ($items as $item) {
                    $uri = sprintf(Articles\Helpers::URL_KEY_PATTERN, $item);

                    $bool = $this->articlesModel->delete($item);

                    if ($this->manageMenuItemHelper) {
                        $this->manageMenuItemHelper->manageMenuItem($uri, false);
                    }

                    $this->articlesCache->getCacheDriver()->delete(Articles\Cache::CACHE_ID . $item);

                    if ($this->uriAliasManager) {
                        $this->uriAliasManager->deleteUriAlias($uri);
                    }
                }

                if ($this->menusCache) {
                    $this->menusCache->saveMenusCache();
                }

                return $bool;
            }
        );
    }
}
