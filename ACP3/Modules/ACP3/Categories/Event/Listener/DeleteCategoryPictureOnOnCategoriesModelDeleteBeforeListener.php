<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Event\Listener;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Categories\Installer\Schema;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;

class DeleteCategoryPictureOnOnCategoriesModelDeleteBeforeListener
{
    /**
     * @var CategoriesRepository
     */
    private $categoryRepository;
    /**
     * @var ApplicationPath
     */
    private $appPath;

    /**
     * OnCategoriesModelDeleteBeforeListener constructor.
     * @param ApplicationPath $appPath
     * @param CategoriesRepository $categoryRepository
     */
    public function __construct(ApplicationPath $appPath, CategoriesRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->appPath = $appPath;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $entryId) {
            $category = $this->categoryRepository->getCategoryDeleteInfosById($entryId);

            $upload = new Upload($this->appPath, Schema::MODULE_NAME);
            $upload->removeUploadedFile($category['picture']);
        }
    }
}
