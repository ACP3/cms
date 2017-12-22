<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Categories\Controller\Admin\Index
 */
class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Cache
     */
    protected $categoriesCache;
    /**
     * @var Categories\Model\CategoriesModel
     */
    protected $categoriesModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Categories\Model\CategoriesModel $categoriesModel
     * @param \ACP3\Modules\ACP3\Categories\Cache $categoriesCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Categories\Model\CategoriesModel $categoriesModel,
        Categories\Cache $categoriesCache
    ) {
        parent::__construct($context);

        $this->categoriesCache = $categoriesCache;
        $this->categoriesModel = $categoriesModel;
    }

    /**
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                $result = $this->categoriesModel->delete($items);

                $this->categoriesCache->getCacheDriver()->deleteAll();

                return $result;
            }
        );
    }
}
