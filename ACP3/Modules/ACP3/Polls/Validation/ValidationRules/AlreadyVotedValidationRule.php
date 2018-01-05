<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollVotesRepository;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class AlreadyVotedValidationRule extends AbstractValidationRule
{
    /**
     * @var UserModel
     */
    protected $userModel;
    /**
     * @var PollVotesRepository
     */
    protected $voteRepository;

    /**
     * AlreadyVotedValidationRule constructor.
     * @param UserModel $userModel
     * @param PollVotesRepository $voteRepository
     */
    public function __construct(
        UserModel $userModel,
        PollVotesRepository $voteRepository
    ) {
        $this->userModel = $userModel;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param mixed $data
     * @param string $field
     * @param array $extra
     *
     * @return boolean
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->getVotes($extra['poll_id'], $extra['ip_address']) == 0;
    }

    /**
     * @param int $pollId
     * @param string $ipAddress
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
