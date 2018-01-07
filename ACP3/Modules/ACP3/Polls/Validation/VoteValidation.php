<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Validation;

use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Core\Validation\ValidationRules\FormTokenValidationRule;
use ACP3\Core\Validation\ValidationRules\NotEmptyValidationRule;
use ACP3\Modules\ACP3\Polls\Validation\ValidationRules\AlreadyVotedValidationRule;

class VoteValidation extends AbstractFormValidation
{
    /**
     * @var int
     */
    protected $pollId = 0;
    /**
     * @var string
     */
    protected $ipAddress = '';

    /**
     * @param int $pollId
     *
     * @return $this
     */
    public function setPollId($pollId)
    {
        $this->pollId = $pollId;

        return $this;
    }

    /**
     * @param string $ipAddress
     *
     * @return $this
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Validation\Exceptions\InvalidFormTokenException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(FormTokenValidationRule::class)
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
