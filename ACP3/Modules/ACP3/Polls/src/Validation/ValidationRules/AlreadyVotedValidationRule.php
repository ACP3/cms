<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation\ValidationRules;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Polls\Repository\VoteRepository;

class AlreadyVotedValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    protected $userModel;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Repository\VoteRepository
     */
    protected $voteRepository;

    public function __construct(
        UserModelInterface $userModel,
        VoteRepository $voteRepository
    ) {
        $this->userModel = $userModel;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param mixed  $data
     * @param string $field
     *
     * @return bool
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->getVotes($extra['poll_id'], $extra['ip_address']) == 0;
    }

    /**
     * @param int    $pollId
     * @param string $ipAddress
     *
     * @return mixed
     */
    protected function getVotes($pollId, $ipAddress)
    {
        // Check, whether the logged user has already voted
        if ($this->userModel->isAuthenticated() === true) {
            $votes = $this->voteRepository->getVotesByUserId($pollId, $this->userModel->getUserId(), $ipAddress);
        } else { // For guest users check against the ip address
            $votes = $this->voteRepository->getVotesByIpAddress($pollId, $ipAddress);
        }

        return $votes;
    }
}
