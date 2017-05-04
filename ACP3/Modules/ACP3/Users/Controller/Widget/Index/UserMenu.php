<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class UserMenu
 * @package ACP3\Modules\ACP3\Users\Controller\Widget\Index
 */
class UserMenu extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * UserMenu constructor.
     * @param WidgetContext $context
     * @param Core\View\Block\BlockInterface $block
     */
    public function __construct(WidgetContext $context, Core\View\Block\BlockInterface $block)
    {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * Displays the user menu, if the user is logged in
     *
     * @return array|bool
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        if ($this->user->isAuthenticated() === true) {
            return $this->block->render();
        }

        return false;
    }
}
