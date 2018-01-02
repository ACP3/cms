<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Archive;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewslettersRepository
     */
    protected $newsletterRepository;
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * Details constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     * @param \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewslettersRepository $newsletterRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block,
        Newsletter\Model\Repository\NewslettersRepository $newsletterRepository
    ) {
        parent::__construct($context);

        $this->newsletterRepository = $newsletterRepository;
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
        $newsletter = $this->newsletterRepository->getOneByIdAndStatus($id, Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_CONFIRMED);

        if (!empty($newsletter)) {
            $this->setCacheResponseCacheable();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
