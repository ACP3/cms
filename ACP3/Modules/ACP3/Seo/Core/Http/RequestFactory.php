<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Http;

use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
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
     * @var Modules
     */
    private $modules;

    /**
     * RequestFactory constructor.
     * @param SettingsInterface $config
     * @param Modules $modules
     * @param SymfonyRequest $symfonyRequest
     * @param SeoRepository $seoRepository
     */
    public function __construct(
        SettingsInterface $config,
        Modules $modules,
        SymfonyRequest $symfonyRequest,
        SeoRepository $seoRepository
    ) {
        parent::__construct($config, $symfonyRequest);

        $this->seoRepository = $seoRepository;
        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    protected function getRequest()
    {
        if ($this->modules->isActive(Schema::MODULE_NAME)) {
            return new Request($this->symfonyRequest, $this->seoRepository);
        }

        return new \ACP3\Core\Http\Request($this->symfonyRequest);
    }
}
