<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Event\Listener;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Categories\Installer\Schema;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;

class DeleteCategoryPictureOnOnCategoriesModelDeleteBeforeListener
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ApplicationPath
     */
    private $appPath;

    /**
     * OnCategoriesModelDeleteBeforeListener constructor.
     * @param ApplicationPath $appPath
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(ApplicationPath $appPath, CategoryRepository $categoryRepository)
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
