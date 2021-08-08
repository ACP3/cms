<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Services;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Modules\ACP3\Emoticons\Repository\EmoticonRepository;

class EmoticonService implements EmoticonServiceInterface
{
    /**
     * @var EmoticonRepository
     */
    private $emoticonRepository;
    /**
     * @var ApplicationPath
     */
    private $applicationPath;

    public function __construct(ApplicationPath $applicationPath, EmoticonRepository $emoticonRepository)
    {
        $this->emoticonRepository = $emoticonRepository;
        $this->applicationPath = $applicationPath;
    }

    /**
     * {@inheritDoc}
     */
    public function getEmoticonList(): array
    {
        $emoticons = $this->emoticonRepository->getAll();

        $data = [];
        foreach ($emoticons as $emoticon) {
            $picInfos = getimagesize($this->applicationPath->getUploadsDir() . 'emoticons/' . $emoticon['img']);
            $code = $emoticon['code'];
            $description = $emoticon['description'];
            $data[$code] = '<img src="' . $this->applicationPath->getWebRoot() . 'uploads/emoticons/' . $emoticon['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
        }

        return $data;
    }
}
