<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Errors\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class AccessForbidden extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * AccessForbidden constructor.
     * @param Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     */
    public function __construct(Context\FrontendContext $context, Core\View\Block\BlockInterface $block)
    {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $this->response->setStatusCode(Response::HTTP_FORBIDDEN);

        return $this->block->render();
    }
}
