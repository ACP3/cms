<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation\ValidationRules;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Polls\Repository\VoteRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AlreadyVotedValidationRule extends AbstractValidationRule
{
    public function __construct(protected UserModelInterface $userModel, protected VoteRepository $voteRepository)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
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
