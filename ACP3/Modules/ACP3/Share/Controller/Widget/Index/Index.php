<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Widget\Index;

use ACP3\Core\Cache\CacheResponseTrait;
use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;
use ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository;

class Index extends AbstractWidgetAction
{
    use CacheResponseTrait;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialServices
     */
    private $socialServices;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository
     */
    private $shareRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\WidgetContext               $context
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialServices           $socialServices
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\ShareRepository $shareRepository
     */
    public function __construct(
        WidgetContext $context,
        SocialServices $socialServices,
        ShareRepository $shareRepository)
    {
        parent::__construct($context);

        $this->socialServices = $socialServices;
        $this->shareRepository = $shareRepository;
    }

    /**
     * @param string $path
     * @param string $template
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(string $path, string $template = ''): array
    {
        $this->setCacheResponseCacheable(3600);
        $this->setTemplate($template);

        $path = \urldecode($path);
        $sharingInfo = $this->shareRepository->getOneByUri($path);

        return [
            'shariff' => [
                'lang' => $this->translator->getShortIsoCode(),
                'path' => $path,
                'services' => $this->getServices($sharingInfo),
            ],
        ];
    }

    /**
     * @param array $sharingInfo
     *
     * @return array
     */
    private function getServices(array $sharingInfo): array
    {
        $services = [];
        if (!empty($sharingInfo['services'])) {
            $services = \unserialize($sharingInfo['services']);
        }
        if (empty($services)) {
            $services = $this->socialServices->getActiveServices();
        }

        return \array_values($services);
    }
}
