<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Result extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository
     */
    protected $pollRepository;
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * Result constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository $pollRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block,
        Core\Date $date,
        Polls\Model\Repository\PollRepository $pollRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
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
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        if ($this->pollRepository->pollExists($id, $this->date->getCurrentDateTime()) === true) {
            return $this->block
                ->setData(['poll_id' => $id])
                ->render();
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
