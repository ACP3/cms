<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Files\Controller\Frontend\Index
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Cache
     */
    protected $categoriesCache;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Categories\Cache           $categoriesCache
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Categories\Cache $categoriesCache
    ) {
        parent::__construct($context);

        $this->categoriesCache = $categoriesCache;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return [
            'categories' => $this->categoriesCache->getCache('files')
        ];
    }
}
