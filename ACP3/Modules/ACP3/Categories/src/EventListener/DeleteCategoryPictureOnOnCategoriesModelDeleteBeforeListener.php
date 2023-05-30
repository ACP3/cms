<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\EventListener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\AfterModelDeleteEvent;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteCategoryPictureOnOnCategoriesModelDeleteBeforeListener implements EventSubscriberInterface
{
    public function __construct(private readonly Upload $categoriesUploadHelper, private readonly CategoryRepository $categoryRepository)
    {
    }

    public function __invoke(AfterModelDeleteEvent $event): void
    {
        foreach ($event->getEntryIdList() as $entryId) {
            $category = $this->categoryRepository->getCategoryDeleteInfosById($entryId);

            $this->categoriesUploadHelper->removeUploadedFile($category['picture']);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'categories.model.categories.before_delete' => '__invoke',
        ];
    }
}
