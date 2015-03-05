<?php
namespace ACP3\Modules\ACP3\Emoticons;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Emoticons
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'list';
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model
     */
    protected $emoticonsModel;

    /**
     * @param Core\Cache $cache
     * @param Model $emoticonsModel
     */
    public function __construct(
        Core\Cache $cache,
        Model $emoticonsModel
    ) {
        parent::__construct($cache);

        $this->emoticonsModel = $emoticonsModel;
    }

    /**
     * Bindet die gecacheten Emoticons ein
     *
     * @return array
     */
    public function getCache()
    {
        if ($this->cache->contains(static::CACHE_ID) === false) {
            $this->setCache();
        }

        return $this->cache->fetch(static::CACHE_ID);
    }

    /**
     * Caches the emoticons
     *
     * @return boolean
     */
    public function setCache()
    {
        $emoticons = $this->emoticonsModel->getAll();
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
