<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Seo;

class Delete extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    private $seoCache;
    /**
     * @var Seo\Model\SeoModel
     */
    private $seoModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Seo\Cache $seoCache,
        Seo\Model\SeoModel $seoModel
    ) {
        parent::__construct($context);

        $this->seoCache = $seoCache;
        $this->seoModel = $seoModel;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
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
