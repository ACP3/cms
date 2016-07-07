<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model;

use ACP3\Core;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;
use ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository;
use ACP3\Modules\ACP3\Users\Model\UserModel;

/**
 * Class VoteModel
 * @package ACP3\Modules\ACP3\Polls\Model
 */
class VoteModel
{
    /**
     * @var VoteRepository
     */
    protected $voteRepository;
    /**
     * @var UserModel
     */
    protected $userModel;
    /**
     * @var Core\Validation\Validator
     */
    protected $validator;

    /**
     * PollsModel constructor.
     * @param Core\Validation\Validator $validator
     * @param UserModel $userModel
     * @param VoteRepository $voteRepository
     */
    public function __construct(
        Core\Validation\Validator $validator,
        UserModel $userModel,
        VoteRepository $voteRepository
    ) {
        $this->validator = $validator;
        $this->userModel = $userModel;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param array $formData
     * @param int $pollId
     * @param string $ipAddress
     * @param string $time
     * @return bool|int
     * @throws Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function vote(array $formData, $pollId, $ipAddress, $time)
    {
        $answers = $formData['answer'];

        $bool = false;
        $userId = $this->userModel->isAuthenticated() ? $this->userModel->getUserId() : null;

        // Multiple Answers
        if (is_array($answers) === false) {
            $answers = [$answers];
        }

        foreach ($answers as $answer) {
            if ($this->validator->is(IntegerValidationRule::class, $answer) === true) {
                $insertValues = [
                    'poll_id' => $pollId,
                    'answer_id' => $answer,
                    'user_id' => $userId,
                    'ip' => $ipAddress,
                    'time' => $time,
                ];
                $bool = $this->voteRepository->insert($insertValues);
            }
        }
        return $bool;
    }
}
