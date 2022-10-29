<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\EventListener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Modules\ACP3\Emoticons\Repository\EmoticonRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnEmoticonsModelBeforeDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly Upload $emoticonsUploadHelper, protected EmoticonRepository $emoticonRepository)
    {
    }

    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        foreach ($event->getEntryId() as $entryId) {
            $this->emoticonsUploadHelper->removeUploadedFile($this->emoticonRepository->getOneImageById($entryId));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'emoticons.model.emoticons.before_delete' => '__invoke',
        ];
    }
}
