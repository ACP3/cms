<?php
namespace ACP3\Modules\Seo;

use ACP3\Core;

/**
 * Class Cache
 * @package ACP3\Modules\Seo
 */
class Cache
{
    /**
     * @var \ACP3\Core\Cache
     */
    protected $seoCache;
    /**
     * @var \ACP3\Modules\Seo\Model
     */
    protected $seoModel;

    /**
     * @param \ACP3\Core\Cache        $seoCache
     * @param \ACP3\Modules\Seo\Model $seoModel
     */
    public function __construct(
        Core\Cache $seoCache,
        Model $seoModel)
    {
        $this->seoCache = $seoCache;
        $this->seoModel = $seoModel;
    }

    /**
     * Gibt den Cache der URI-Aliase zurück
     *
     * @return array
     */
    public function getCache()
    {
        if ($this->seoCache->contains('seo') === false) {
            $this->setCache();
        }

        return $this->seoCache->fetch('seo');
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

        return $this->seoCache->save('seo', $data);
    }
}