<?php
namespace ACP3\Modules\ACP3\Seo;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Seo
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model
     */
    protected $seoModel;

    /**
     * @param \ACP3\Core\Cache             $cache
     * @param \ACP3\Modules\ACP3\Seo\Model $seoModel
     */
    public function __construct(
        Core\Cache $cache,
        Model $seoModel)
    {
        parent::__construct($cache);

        $this->seoModel = $seoModel;
    }

    /**
     * Gibt den Cache der URI-Aliase zurück
     *
     * @return array
     */
    public function getCache()
    {
        if ($this->cache->contains('seo') === false) {
            $this->setCache();
        }

        return $this->cache->fetch('seo');
    }

    /**
     * Setzt den Cache für die URI-Aliase
     *
     * @return boolean
     */
    public function setCache()
    {
        $aliases = $this->seoModel->getAllMetaTags();
        $c_aliases = count($aliases);
        $data = [];

        for ($i = 0; $i < $c_aliases; ++$i) {
            $data[$aliases[$i]['uri']] = [
                'alias' => $aliases[$i]['alias'],
                'keywords' => $aliases[$i]['keywords'],
                'description' => $aliases[$i]['description'],
                'robots' => $aliases[$i]['robots']
            ];
        }

        return $this->cache->save('seo', $data);
    }
}