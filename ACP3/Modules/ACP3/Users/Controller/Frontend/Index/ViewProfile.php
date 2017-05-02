<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users;

class ViewProfile extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * ViewProfile constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block
    ) {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $user = $this->user->getUserInfo($id);

        if (!empty($user)) {
            $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            return $this->block
                ->setData($user)
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
