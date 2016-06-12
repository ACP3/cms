<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Categories\Controller\Admin\Index
 */
class Delete extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Cache
     */
    protected $categoriesCache;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext             $context
     * @param \ACP3\Modules\ACP3\Categories\Model\CategoryRepository $categoryRepository
     * @param \ACP3\Modules\ACP3\Categories\Cache                    $categoriesCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Categories\Model\CategoryRepository $categoryRepository,
        Categories\Cache $categoriesCache)
    {
        parent::__construct($context);

        $this->categoryRepository = $categoryRepository;
        $this->categoriesCache = $categoriesCache;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;
                $isInUse = false;

                foreach ($items as $item) {
                    if (!empty($item) && $this->categoryRepository->resultExists($item) === true) {
                        $category = $this->categoryRepository->getCategoryDeleteInfosById($item);

                        $serviceId = strtolower($category['module'] . '.' . $category['module'] . 'repository');
                        if ($this->container->has($serviceId) &&
                            $this->get($serviceId)->countAll('', $item) > 0
                        ) {
                            $isInUse = true;
                            continue;
                        }

                        // Kategoriebild ebenfalls löschen
                        $upload = new Core\Helpers\Upload($this->appPath, 'categories');
                        $upload->removeUploadedFile($category['picture']);
                        $bool = $this->categoryRepository->delete($item);
                    }
                }

                $this->categoriesCache->getCacheDriver()->deleteAll();

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                if ($isInUse === true) {
                    $text = $this->translator->t('categories', 'category_is_in_use');
                    $bool = false;
                } else {
                    $text = $this->translator->t('system', $bool !== false ? 'delete_success' : 'delete_error');
                }

                return $this->redirectMessages()->setMessage($bool, $text);
            }
        );
    }
}
