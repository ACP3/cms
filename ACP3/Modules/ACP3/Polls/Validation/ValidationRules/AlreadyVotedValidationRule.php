<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation\ValidationRules;


use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository;
use ACP3\Modules\ACP3\Users\Model\UserModel;

/**
 * Class AlreadyVotedValidationRule
 * @package ACP3\Modules\ACP3\Polls\Validation\ValidationRules
 */
class AlreadyVotedValidationRule extends AbstractValidationRule
{
    /**
     * @var UserModel
     */
    protected $userModel;
    /**
     * @var VoteRepository
     */
    protected $voteRepository;

    /**
     * AlreadyVotedValidationRule constructor.
     * @param UserModel $userModel
     * @param VoteRepository $voteRepository
     */
    public function __construct(
        UserModel $userModel,
        VoteRepository $voteRepository)
    {
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
