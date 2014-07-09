<?php
namespace ACP3\Modules\Emoticons;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\Emoticons
 */
class Cache
{
    const CACHE_ID = 'list';
    /**
     * @var \ACP3\Core\Cache2
     */
    protected $cache;
    /**
     * @var Model
     */
    protected $emoticonsModel;

    public function __construct(Model $emoticonsModel)
    {
        $this->cache = new Core\Cache2('emoticons');
        $this->emoticonsModel = $emoticonsModel;
    }

    /**
     * Cache die Emoticons
     *
     * @return boolean
     */
    public function setCache()
    {
        $emoticons = $this->emoticonsModel->getAll();
        $c_emoticons = count($emoticons);

        $data = array();
        for ($i = 0; $i < $c_emoticons; ++$i) {
            $picInfos = getimagesize(UPLOADS_DIR . 'emoticons/' . $emoticons[$i]['img']);
            $code = $emoticons[$i]['code'];
            $description = $emoticons[$i]['description'];
            $data[$code] = '<img src="' . ROOT_DIR . 'uploads/emoticons/' . $emoticons[$i]['img'] . '" width="' . $picInfos[0] . '" height="' . $picInfos[1] . '" alt="' . $description . '" title="' . $description . '" />';
        }

        return $this->cache->save(static::CACHE_ID, $data);
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


} 