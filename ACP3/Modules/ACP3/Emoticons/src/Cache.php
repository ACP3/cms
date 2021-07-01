<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository;

class Cache extends Core\Modules\AbstractCacheStorage
{
    public const CACHE_ID = 'list';

    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository
     */
    private $emoticonRepository;

    public function __construct(
        Core\Cache $cache,
        Core\Environment\ApplicationPath $appPath,
        EmoticonRepository $emoticonRepository
    ) {
        parent::__construct($cache);

        $this->appPath = $appPath;
        $this->emoticonRepository = $emoticonRepository;
    }

    /**
     * Bindet die gecacheten Emoticons ein.
     */
    public function getCache(): array
    {
        if ($this->cache->contains(static::CACHE_ID) === false) {
            $this->saveCache();
        }

        return $this->cache->fetch(static::CACHE_ID);
    }

    /**
     * Caches the emoticons.
     */
    public function saveCache(): bool
    {
        $emoticons = $this->emoticonRepository->getAll();

        $data = [];
        foreach ($emoticons as $emoticon) {
            $picInfos = getimagesize($this->appPath->getUploadsDir() . 'emoticons/' . $emoticon['img']);
            $code = $emoticon['code'];
            $description = $emoticon['description'];
            $data[$code] = '<img src="' . $this->appPath->getWebRoot() . 'uploads/emoticons/' . $emoticon['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
        }

        return $this->cache->save(static::CACHE_ID, $data);
    }
}
