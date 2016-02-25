<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Http;

use ACP3\Core\Config;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Modules\ACP3\Seo\Model\SeoRepository;

/**
 * Class RequestFactory
 * @package ACP3\Modules\ACP3\Seo\Core\Http
 */
class RequestFactory extends \ACP3\Core\Http\RequestFactory
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\SeoRepository
     */
    protected $seoRepository;

    /**
     * RequestFactory constructor.
     *
     * @param \ACP3\Core\Config                          $config
     * @param \ACP3\Core\Environment\ApplicationPath     $appPath
     * @param \ACP3\Modules\ACP3\Seo\Model\SeoRepository $seoRepository
     */
    public function __construct(Config $config, ApplicationPath $appPath, SeoRepository $seoRepository)
    {
        parent::__construct($config, $appPath);

        $this->seoRepository = $seoRepository;
    }

    /**
     * @return \ACP3\Modules\ACP3\Seo\Core\Http\Request
     */
    protected function getRequest()
    {
        return new Request($this->appPath, $this->seoRepository);
    }
}
