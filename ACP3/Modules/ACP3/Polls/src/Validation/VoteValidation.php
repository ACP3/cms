<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation;

use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Core\Validation\ValidationRules\NotEmptyValidationRule;
use ACP3\Modules\ACP3\Polls\Validation\ValidationRules\AlreadyVotedValidationRule;

class VoteValidation extends AbstractFormValidation
{
    private int $pollId = 0;
    private string $ipAddress = '';

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withPollId instead.
     */
    public function setPollId(int $pollId): static
    {
        $this->pollId = $pollId;

        return $this;
    }

    public function withPollId(int $pollId): static
    {
        $clone = clone $this;
        $clone->pollId = $pollId;

        return $clone;
    }

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withIpAddress instead.
     */
    public function setIpAddress(string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function withIpAddress(string $ipAddress): static
    {
        $clone = clone $this;
        $clone->ipAddress = $ipAddress;

        return $clone;
    }

    /**
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     */
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(NotEmptyValidationRule::class, [
                'data' => $formData,
                'field' => 'answer',
                'message' => $this->translator->t('polls', 'select_answer'),
            ])
            ->addConstraint(AlreadyVotedValidationRule::class, [
                'message' => $this->translator->t('polls', 'already_voted'),
                'extra' => [
                    'poll_id' => $this->pollId,
                    'ip_address' => $this->ipAddress,
                ],
            ]);

        $this->validator->validate();
    }
}
