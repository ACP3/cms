<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var Seo\Model\SeoModel
     */
    protected $seoModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Seo\Cache $seoCache
     * @param Seo\Model\SeoModel $seoModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Seo\Cache $seoCache,
        Seo\Model\SeoModel $seoModel
    ) {
        parent::__construct($context);

        $this->seoCache = $seoCache;
        $this->seoModel = $seoModel;
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
                $result = $this->seoModel->delete($items);

                $this->seoCache->saveCache();

                return $result;
            }
        );
    }
}
