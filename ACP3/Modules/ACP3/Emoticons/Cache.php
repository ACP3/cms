<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Emoticons
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'list';
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository
     */
    protected $emoticonRepository;

    /**
     * @param \ACP3\Core\Cache                                      $cache
     * @param \ACP3\Core\Environment\ApplicationPath                $appPath
     * @param \ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository $emoticonRepository
     */
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
     * Bindet die gecacheten Emoticons ein
     *
     * @return array
     */
    public function getCache()
    {
        if ($this->cache->contains(static::CACHE_ID) === false) {
            $this->saveCache();
        }

        return $this->cache->fetch(static::CACHE_ID);
    }

    /**
     * Caches the emoticons
     *
     * @return boolean
     */
    public function saveCache()
    {
        $emoticons = $this->emoticonRepository->getAll();
        $cEmoticons = count($emoticons);

        $data = [];
        for ($i = 0; $i < $cEmoticons; ++$i) {
            $picInfos = getimagesize($this->appPath->getUploadsDir() . 'emoticons/' . $emoticons[$i]['img']);
            $code = $emoticons[$i]['code'];
            $description = $emoticons[$i]['description'];
            $data[$code] = '<img src="' . $this->appPath->getWebRoot() . 'uploads/emoticons/' . $emoticons[$i]['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
        }

        return $this->cache->save(static::CACHE_ID, $data);
    }
}
