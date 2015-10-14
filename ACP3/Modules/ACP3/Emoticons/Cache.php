<?php
namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Emoticons
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'list';
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository
     */
    protected $emoticonRepository;

    /**
     * @param \ACP3\Core\Cache                                      $cache
     * @param \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository $emoticonRepository
     */
    public function __construct(
        Core\Cache $cache,
        EmoticonRepository $emoticonRepository
    )
    {
        parent::__construct($cache);

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
        $c_emoticons = count($emoticons);

        $data = [];
        for ($i = 0; $i < $c_emoticons; ++$i) {
            $picInfos = getimagesize(UPLOADS_DIR . 'emoticons/' . $emoticons[$i]['img']);
            $code = $emoticons[$i]['code'];
            $description = $emoticons[$i]['description'];
            $data[$code] = '<img src="' . ROOT_DIR . 'uploads/emoticons/' . $emoticons[$i]['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
        }

        return $this->cache->save(static::CACHE_ID, $data);
    }
}
