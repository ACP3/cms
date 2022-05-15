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
    public function __construct(private ApplicationPath $applicationPath, private EmoticonRepository $emoticonRepository)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getEmoticonList(): array
    {
        $emoticons = $this->emoticonRepository->getAll();

        $data = [];
        foreach ($emoticons as $emoticon) {
            $path = $this->applicationPath->getUploadsDir() . 'emoticons/' . $emoticon['img'];

            if (is_file($path)) {
                $picInfos = getimagesize($this->applicationPath->getUploadsDir() . 'emoticons/' . $emoticon['img']);
                $code = $emoticon['code'];
                $description = $emoticon['description'];
                $data[$code] = '<img src="' . $this->applicationPath->getWebRoot() . 'uploads/emoticons/' . $emoticon['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
            }
        }

        return $data;
    }
}
