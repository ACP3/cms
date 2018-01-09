<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model;

use ACP3\Core;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;
use ACP3\Modules\ACP3\Polls\Installer\Schema;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollVotesRepository;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PollVotesModel extends Core\Model\AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var UserModel
     */
    protected $userModel;
    /**
     * @var Core\Validation\Validator
     */
    protected $validator;

    /**
     * PollVotesModel constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface   $eventDispatcher
     * @param \ACP3\Core\Model\DataProcessor                                $dataProcessor
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollVotesRepository $repository
     * @param \ACP3\Core\Validation\Validator                               $validator
     * @param \ACP3\Modules\ACP3\Users\Model\UserModel                      $userModel
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        PollVotesRepository $repository,
        Core\Validation\Validator $validator,
        UserModel $userModel)
    {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);

        $this->validator = $validator;
        $this->userModel = $userModel;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $answers = $rawData['answer'];

        $userId = $this->userModel->isAuthenticated() ? $this->userModel->getUserId() : null;

        // Multiple Answers
        if (\is_array($answers) === false) {
            $answers = [$answers];
        }

        $affectedRows = 0;
        foreach ($answers as $answer) {
            if ($this->validator->is(IntegerValidationRule::class, $answer) === true) {
                $insertValues = [
                    'poll_id' => $rawData['poll_id'],
                    'answer_id' => $answer,
                    'user_id' => $userId,
                    'ip' => $rawData['ip'],
                    'time' => $rawData['time'],
                ];
                parent::save($insertValues, $entryId);

                ++$affectedRows;
            }
        }

        return $affectedRows;
    }

    /**
     * @param array  $formData
     * @param int    $pollId
     * @param string $ipAddress
     * @param string $time
     *
     * @return bool|int
     *
     * @throws Core\Validation\Exceptions\ValidationRuleNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function vote(array $formData, int $pollId, string $ipAddress, string $time)
    {
        $answers = $formData['answer'];

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
                $bool = $this->repository->insert($insertValues);
            }
        }

        return $bool;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedColumns()
    {
        return [
            'poll_id' => Core\Model\DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'answer_id' => Core\Model\DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'user_id' => Core\Model\DataProcessor\ColumnTypes::COLUMN_TYPE_INT_NULLABLE,
            'ip' => Core\Model\DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'time' => Core\Model\DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
        ];
    }
}
