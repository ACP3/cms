<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model;

use ACP3\Core;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;
use ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository;

class VoteModel
{
    /**
     * @var VoteRepository
     */
    protected $voteRepository;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    protected $userModel;
    /**
     * @var Core\Validation\Validator
     */
    protected $validator;

    public function __construct(
        Core\Validation\Validator $validator,
        Core\Authentication\Model\UserModelInterface $userModel,
        VoteRepository $voteRepository
    ) {
        $this->validator = $validator;
        $this->userModel = $userModel;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param int    $pollId
     * @param string $ipAddress
     * @param string $time
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function vote(array $formData, $pollId, $ipAddress, $time)
    {
        $answers = $formData['answer'];

        $bool = false;
        $userId = $this->userModel->isAuthenticated() ? $this->userModel->getUserId() : null;

        // Multiple Answers
        if (\is_array($answers) === false) {
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
