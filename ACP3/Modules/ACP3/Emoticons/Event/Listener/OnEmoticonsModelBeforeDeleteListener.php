<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Event\Listener;


use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Upload;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository;

class OnEmoticonsModelBeforeDeleteListener
{
    /**
     * @var ApplicationPath
     */
    protected $applicationPath;
    /**
     * @var EmoticonRepository
     */
    protected $emoticonRepository;

    /**
     * OnEmoticonsModelBeforeDeleteListener constructor.
     * @param ApplicationPath $applicationPath
     * @param EmoticonRepository $emoticonRepository
     */
    public function __construct(
        ApplicationPath $applicationPath,
        EmoticonRepository $emoticonRepository
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

        $upload = new Upload($this->applicationPath, 'emoticons');
        foreach ($event->getEntryId() as $entryId) {
            if (!empty($entryId) && $this->emoticonRepository->resultExists($entryId) === true) {
                $file = $this->emoticonRepository->getOneImageById($entryId);
                $upload->removeUploadedFile($file);
            }
        }
    }
}
