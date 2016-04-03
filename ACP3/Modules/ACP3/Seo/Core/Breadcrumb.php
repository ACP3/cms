<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core;


use ACP3\Core\Config;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Breadcrumb
 * @package ACP3\Modules\ACP3\Seo\Core
 */
class Breadcrumb extends \ACP3\Core\Breadcrumb
{
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * Breadcrumb constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \ACP3\Core\I18n\Translator                                $translator
     * @param \ACP3\Core\Http\RequestInterface                          $request
     * @param \ACP3\Core\RouterInterface                                $router
     * @param \ACP3\Core\Config                                         $config
     */
    public function __construct(
        ContainerInterface $container,
        Translator $translator,
        RequestInterface $request,
        RouterInterface $router,
        Config $config
    ) {
        parent::__construct($container, $translator, $request, $router);

        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getSiteTitle()
    {
        return $this->config->getSettings('seo')['title'];
    }
}
