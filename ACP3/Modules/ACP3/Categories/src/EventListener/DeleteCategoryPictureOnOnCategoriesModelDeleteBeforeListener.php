<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\EventListener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteCategoryPictureOnOnCategoriesModelDeleteBeforeListener implements EventSubscriberInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $categoriesUploadHelper;

    public function __construct(
        Upload $categoriesUploadHelper,
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoriesUploadHelper = $categoriesUploadHelper;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $entryId) {
            $category = $this->categoryRepository->getCategoryDeleteInfosById($entryId);

            $this->categoriesUploadHelper->removeUploadedFile($category['picture']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'categories.model.categories.before_delete' => '__invoke',
        ];
    }
}
