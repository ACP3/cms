<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;

class Login extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * Login constructor.
     * @param WidgetContext $context
     * @param Core\View\Block\FormBlockInterface $block
     */
    public function __construct(WidgetContext $context, Core\View\Block\FormBlockInterface $block)
    {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * Displays the login mask, if the user is not already logged in
     *
     * @return array|null
     */
    public function execute()
    {
        $this->setCacheResponseCacheable(
            $this->config->getSettings(\ACP3\Modules\ACP3\System\Installer\Schema::MODULE_NAME)['cache_lifetime']
        );

        if ($this->user->isAuthenticated() === false) {
            $prefix = $this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN ? 'acp/' : '';
            $currentPage = base64_encode($prefix . $this->request->getQuery());

            return $this->block
                ->setData([
                    'redirect_url' => $this->request->getPost()->get('redirect_uri', $currentPage)
                ])
                ->render();
        }

        $this->setContent(false);

        return null;
    }
}
