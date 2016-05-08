<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin\Index
 */
class Delete extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\SeoRepository
     */
    protected $seoRepository;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Modules\ACP3\Seo\Cache               $seoCache
     * @param \ACP3\Modules\ACP3\Seo\Model\SeoRepository $seoRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Seo\Cache $seoCache,
        Seo\Model\SeoRepository $seoRepository
    ) {
        parent::__construct($context);

        $this->seoCache = $seoCache;
        $this->seoRepository = $seoRepository;
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
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    $bool = $this->seoRepository->delete($item);
                }

                $this->seoCache->saveCache();

                return $bool;
            }
        );
    }
}
