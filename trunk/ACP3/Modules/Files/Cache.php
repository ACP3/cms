<?php
namespace ACP3\Modules\Files;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\Files
 */
class Cache
{
    /**
     * @var Core\Cache2
     */
    protected $cache;
    /**
     * @var Model
     */
    protected $filesModel;
    /**
     * @var string
     */
    private $cacheId = 'details_id_';

    public function __construct(Model $filesModel)
    {
        $this->filesModel = $filesModel;
        $this->cache = new Core\Cache2('files');
    }

    /**
     * @param integer $id
     * @return boolean
     */
    public function setCache($id)
    {
        return $this->cache->save($this->cacheId . $id, $this->filesModel->getOneById($id));
    }

    /**
     * @param integer $id
     * @return array
     */
    public function getCache($id)
    {
        if ($this->cache->contains($this->cacheId . $id) === false) {
            $this->setCache($id);
        }

        return $this->cache->fetch($this->cacheId . $id);
    }

} 