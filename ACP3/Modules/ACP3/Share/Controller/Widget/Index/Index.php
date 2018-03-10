<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Widget\Index;


use ACP3\Core\Cache\CacheResponseTrait;
use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;

class Index extends AbstractWidgetAction
{
    use CacheResponseTrait;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialServices
     */
    private $socialServices;

    /**
     * Index constructor.
     * @param \ACP3\Core\Controller\Context\WidgetContext     $context
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialServices $socialServices
     */
    public function __construct(WidgetContext $context, SocialServices $socialServices)
    {
        parent::__construct($context);

        $this->socialServices = $socialServices;
    }

    public function execute(string $path, string $template = ''): array
    {
        $this->setCacheResponseCacheable(3600);

        $this->setTemplate($template);

        return [
            'path' => urldecode($path),
            'services' => json_encode($this->socialServices->getActiveServices())
        ];
    }
}
