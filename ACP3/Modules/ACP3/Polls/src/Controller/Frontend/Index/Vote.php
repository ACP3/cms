<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Vote extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository
     */
    private $pollRepository;
    /**
     * @var Polls\Model\VoteModel
     */
    private $pollsModel;
    /**
     * @var Polls\Validation\VoteValidation
     */
    private $voteValidation;
    /**
     * @var \ACP3\Modules\ACP3\Polls\ViewProviders\PollVoteViewProvider
     */
    private $pollVoteViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Polls\Validation\VoteValidation $voteValidation,
        Polls\Model\VoteModel $pollsModel,
        Polls\Model\Repository\PollRepository $pollRepository,
        Polls\ViewProviders\PollVoteViewProvider $pollVoteViewProvider
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->voteValidation = $voteValidation;
        $this->pollsModel = $pollsModel;
        $this->pollRepository = $pollRepository;
        $this->pollVoteViewProvider = $pollVoteViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        if ($this->pollRepository->pollExists($id, $this->date->getCurrentDateTime()) === true) {
            return ($this->pollVoteViewProvider)($id);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($id) {
                $formData = $this->request->getPost()->all();
                $ipAddress = $this->request->getSymfonyRequest()->getClientIp();
                $time = $this->date->getCurrentDateTime();

                $this->voteValidation
                    ->setPollId($id)
                    ->setIpAddress($ipAddress)
                    ->validate($formData);

                $result = $this->pollsModel->vote($formData, $id, $ipAddress, $time);

                $text = $this->translator->t('polls', $result !== false ? 'poll_success' : 'poll_error');

                return $this->redirectMessages()->setMessage($result, $text, 'polls/index/result/id_' . $id);
            },
            'polls/index/vote/id_' . $id
        );
    }
}
