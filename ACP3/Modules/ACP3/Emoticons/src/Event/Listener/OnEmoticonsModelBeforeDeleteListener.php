<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;

use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository;

class OnEmoticonsModelBeforeDeleteListener
{
    /**
     * @var EmoticonRepository
     */
    protected $emoticonRepository;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $emoticonsUploadHelper;

    /**
     * OnEmoticonsModelBeforeDeleteListener constructor.
     */
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
}
