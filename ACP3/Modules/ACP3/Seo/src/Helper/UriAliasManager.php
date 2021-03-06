<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Helper;

use ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository;
use ACP3\Modules\ACP3\Seo\Model\SeoModel;

class UriAliasManager
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository
     */
    protected $seoRepository;
    /**
     * @var SeoModel
     */
    protected $seoModel;

    /**
     * UriAliasManager constructor.
     */
    public function __construct(
        SeoModel $seoModel,
        SeoRepository $seoRepository
    ) {
        $this->seoRepository = $seoRepository;
        $this->seoModel = $seoModel;
    }

    /**
     * Deletes the given URL alias.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteUriAlias(string $path): bool
    {
        $path .= $this->preparePath($path);
        $seo = $this->seoRepository->getOneByUri($path);

        return !empty($seo) && $this->seoModel->delete($seo['id']) !== false;
    }

    protected function preparePath(string $path): string
    {
        return !preg_match('/\/$/', $path) ? '/' : '';
    }

    /**
     * Inserts/Updates a given URL alias.
     */
    public function insertUriAlias(
        string $path,
        string $alias,
        string $keywords = '',
        string $description = '',
        int $robots = 0,
        string $title = ''
    ): bool {
        $path .= $this->preparePath($path);
        $data = [
            'uri' => $path,
            'alias' => $alias,
            'seo_title' => $title,
            'seo_keywords' => $keywords,
            'seo_description' => $description,
            'seo_robots' => (int) $robots,
        ];

        $seo = $this->seoRepository->getOneByUri($path);

        return $this->seoModel->save($data, $seo['id'] ?? null) !== false;
    }
}
