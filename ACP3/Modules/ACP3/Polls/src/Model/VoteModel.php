<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model;

use ACP3\Core;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;
use ACP3\Modules\ACP3\Polls\Repository\VoteRepository;

class VoteModel
{
    public function __construct(protected Core\Validation\Validator $validator, protected Core\Authentication\Model\UserModelInterface $userModel, protected VoteRepository $voteRepository)
    {
    }

    /**
     * @param array<string, mixed> $formData
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function vote(array $formData, int $pollId, string $ipAddress, string $time): bool
    {
        $answers = $formData['answer'];

        $result = false;
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
                $result = $this->voteRepository->insert($insertValues);
            }
        }

        return $result;
    }
}
