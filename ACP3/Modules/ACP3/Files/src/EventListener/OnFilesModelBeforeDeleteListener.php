<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\EventListener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Modules\ACP3\Files\Repository\FilesRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnFilesModelBeforeDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly Upload $filesUploadHelper, private readonly FilesRepository $filesRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        foreach ($event->getEntryIdList() as $item) {
            $this->filesUploadHelper->removeUploadedFile($this->filesRepository->getFileById($item));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'files.model.files.before_delete' => '__invoke',
        ];
    }
}
