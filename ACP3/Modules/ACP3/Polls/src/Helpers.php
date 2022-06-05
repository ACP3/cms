<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Polls\Repository\VoteRepository;

class Helpers
{
    public const URL_KEY_PATTERN = 'polls/index/result/id_%d/';

    public function __construct(private readonly VoteRepository $voteRepository, private readonly RequestInterface $request, private readonly UserModelInterface $user)
    {
    }

    public function hasAlreadyVoted(int $pollId): bool
    {
        // Check, whether the logged user has already voted
        if ($this->user->isAuthenticated() === true) {
            $query = $this->voteRepository->getVotesByUserId(
                $pollId,
                $this->user->getUserId(),
                $this->request->getSymfonyRequest()->getClientIp()
            );
        } else { // For guest users check against the IP-address
            $query = $this->voteRepository->getVotesByIpAddress(
                $pollId,
                $this->request->getSymfonyRequest()->getClientIp()
            );
        }

        return $query > 0;
    }
}
