<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\EventListener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Emoticons\Repository\EmoticonRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnEmoticonsModelBeforeDeleteListener implements EventSubscriberInterface
{
    /**
     * @var EmoticonRepository
     */
    protected $emoticonRepository;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $emoticonsUploadHelper;

    public function __construct(
        Upload $emoticonsUploadHelper,
        EmoticonRepository $emoticonRepository
    ) {
        $this->emoticonRepository = $emoticonRepository;
        $this->emoticonsUploadHelper = $emoticonsUploadHelper;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

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
