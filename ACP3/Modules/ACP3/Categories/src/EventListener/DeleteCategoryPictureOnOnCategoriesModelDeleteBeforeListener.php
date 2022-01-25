<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\EventListener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteCategoryPictureOnOnCategoriesModelDeleteBeforeListener implements EventSubscriberInterface
{
    public function __construct(private Upload $categoriesUploadHelper, private CategoryRepository $categoryRepository)
    {
    }

    public function __invoke(ModelSaveEvent $event): void
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
