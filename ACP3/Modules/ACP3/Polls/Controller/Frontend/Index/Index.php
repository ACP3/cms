<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Polls\Controller\Frontend\Index
 */
class Index extends Core\Controller\AbstractFrontendAction
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
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository
     */
    protected $voteRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository $voteRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Polls\Model\Repository\PollRepository $pollRepository,
        Polls\Model\Repository\VoteRepository $voteRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
        $this->voteRepository = $voteRepository;
    }

    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $polls = $this->pollRepository->getAll($this->date->getCurrentDateTime());
        $cPolls = count($polls);

        for ($i = 0; $i < $cPolls; ++$i) {
            $query = $this->fetchVote($polls[$i]['id']);

            if ($query != 0 ||
                $polls[$i]['start'] !== $polls[$i]['end'] && $this->date->timestamp($polls[$i]['end']) <= $this->date->timestamp()
            ) {
                $polls[$i]['link'] = 'result';
            } else {
                $polls[$i]['link'] = 'vote';
            }
        }

        return [
            'polls' => $polls
        ];
    }

    /**
     * @param int $pollId
     * @return int
     */
    protected function fetchVote($pollId)
    {
        // Check, whether the logged user has already voted
        if ($this->user->isAuthenticated() === true) {
            $query = $this->voteRepository->getVotesByUserId(
                $pollId,
                $this->user->getUserId(),
                $this->request->getServer()->get('REMOTE_ADDR', '')
            );
        } else { // For guest users check against the IP-address
            $query = $this->voteRepository->getVotesByIpAddress(
                $pollId,
                $this->request->getServer()->get('REMOTE_ADDR', '')
            );
        }

        return $query;
    }
}
