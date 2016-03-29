<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Helper;

use ACP3\Core\Helpers\Secure;
use ACP3\Modules\ACP3\Seo\Cache as SeoCache;
use ACP3\Modules\ACP3\Seo\Model\SeoRepository;

/**
 * Class UriAliasManager
 * @package ACP3\Modules\ACP3\Seo\Helper
 */
class UriAliasManager
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Cache
     */
    protected $seoCache;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\SeoRepository
     */
    protected $seoRepository;

    /**
     * UriAliasManager constructor.
     *
     * @param \ACP3\Core\Helpers\Secure                  $secureHelper
     * @param \ACP3\Modules\ACP3\Seo\Cache               $seoCache
     * @param \ACP3\Modules\ACP3\Seo\Model\SeoRepository $seoRepository
     */
    public function __construct(
        Secure $secureHelper,
        SeoCache $seoCache,
        SeoRepository $seoRepository
    ) {
        $this->secureHelper = $secureHelper;
        $this->seoCache = $seoCache;
        $this->seoRepository = $seoRepository;
    }

    /**
     * Deletes the given URL alias
     *
     * @param string $path
     *
     * @return boolean
     */
    public function deleteUriAlias($path)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        $bool = $this->seoRepository->delete($path, 'uri');
        return $bool !== false && $this->seoCache->saveCache() !== false;
    }

    /**
     * Inserts/Updates a given URL alias
     *
     * @param string $path
     * @param string $alias
     * @param string $keywords
     * @param string $description
     * @param int    $robots
     *
     * @return boolean
     */
    public function insertUriAlias($path, $alias, $keywords = '', $description = '', $robots = 0)
    {
        $path .= !preg_match('/\/$/', $path) ? '/' : '';
        $keywords = $this->secureHelper->strEncode($keywords);
        $description = $this->secureHelper->strEncode($description);
        $values = [
            'alias' => $alias,
            'keywords' => $keywords,
            'description' => $description,
            'robots' => (int)$robots
        ];

        // Update an existing result
        if ($this->seoRepository->uriAliasExists($path) === true) {
            $bool = $this->seoRepository->update($values, ['uri' => $path]);
        } else {
            $values['uri'] = $path;
            $bool = $this->seoRepository->insert($values);
        }

        return $bool !== false && $this->seoCache->saveCache() !== false;
    }
}
