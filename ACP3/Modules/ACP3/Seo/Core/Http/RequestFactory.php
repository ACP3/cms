<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Http;

use ACP3\Core\Config;
use ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class RequestFactory
 * @package ACP3\Modules\ACP3\Seo\Core\Http
 */
class RequestFactory extends \ACP3\Core\Http\RequestFactory
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository
     */
    protected $seoRepository;

    /**
     * RequestFactory constructor.
     *
     * @param \ACP3\Core\Config $config
     * @param SymfonyRequest $symfonyRequest
     * @param \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository $seoRepository
     */
    public function __construct(Config $config, SymfonyRequest $symfonyRequest, SeoRepository $seoRepository)
    {
        parent::__construct($config, $symfonyRequest);

        $this->seoRepository = $seoRepository;
    }

    /**
     * @return \ACP3\Modules\ACP3\Seo\Core\Http\Request
     */
    protected function getRequest()
    {
        return new Request($this->symfonyRequest, $this->seoRepository);
    }
}
