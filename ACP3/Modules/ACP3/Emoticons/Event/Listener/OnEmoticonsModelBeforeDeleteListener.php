<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;
use ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonsRepository;

class OnEmoticonsModelBeforeDeleteListener
{
    /**
     * @var ApplicationPath
     */
    protected $applicationPath;
    /**
     * @var EmoticonsRepository
     */
    protected $emoticonRepository;

    /**
     * OnEmoticonsModelBeforeDeleteListener constructor.
     * @param ApplicationPath $applicationPath
     * @param EmoticonsRepository $emoticonRepository
     */
    public function __construct(
        ApplicationPath $applicationPath,
        EmoticonsRepository $emoticonRepository
    ) {
        $this->applicationPath = $applicationPath;
        $this->emoticonRepository = $emoticonRepository;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        $upload = new Upload($this->applicationPath, Schema::MODULE_NAME);
        foreach ($event->getEntryId() as $entryId) {
            $upload->removeUploadedFile($this->emoticonRepository->getOneImageById($entryId));
        }
    }
}
