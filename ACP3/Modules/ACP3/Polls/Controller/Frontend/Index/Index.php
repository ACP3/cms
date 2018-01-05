<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * Index constructor.
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
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setData([
                'ip_address' => $this->request->getSymfonyRequest()->getClientIp(),
                'user_id' => $this->user->getUserId(),
            ])
            ->render();
    }
}
