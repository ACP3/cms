<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Frontend\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;
use Heise\Shariff\Backend;
use Symfony\Component\HttpFoundation\JsonResponse;

class Index extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialServices
     */
    private $socialServices;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext   $context
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialServices $socialServices
     */
    public function __construct(FrontendContext $context, SocialServices $socialServices)
    {
        parent::__construct($context);

        $this->socialServices = $socialServices;
    }

    public function execute()
    {
        $this->checkCacheDir();

        $options = [
            'domains' => [$this->request->getHttpHost()],
            'cache' => [
                'ttl' => 60,
                'cacheDir' => $this->getCacheDir(),
                'adapter' => 'Filesystem',
            ],
            'services' => $this->socialServices->getActiveServices(),
        ];
        $shariff = new Backend($options);

        return new JsonResponse(
            $shariff->get(
                $this->request->getSymfonyRequest()->query->get('url', '')
            )
        );
    }

    private function checkCacheDir(): void
    {
        if (!\is_dir($this->getCacheDir())) {
            \mkdir($this->getCacheDir());
        }
    }

    private function getCacheDir(): string
    {
        return $this->appPath->getCacheDir() . 'shariff/';
    }
}
