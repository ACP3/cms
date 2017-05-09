<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Files extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var Core\View\Block\ListingBlockInterface
     */
    private $block;

    /**
     * Files constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\ListingBlockInterface $block
     * @param \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\ListingBlockInterface $block,
        Categories\Model\Repository\CategoryRepository $categoryRepository)
    {
        parent::__construct($context);

        $this->categoryRepository = $categoryRepository;
        $this->block = $block;
    }

    /**
     * @param int $cat
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($cat)
    {
        if ($this->categoryRepository->resultExists($cat) === true) {
            $this->setCacheResponseCacheable();

            return $this->block
                ->setData(['category_id' => $cat])
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
