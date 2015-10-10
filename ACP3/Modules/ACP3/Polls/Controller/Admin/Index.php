<?php

namespace ACP3\Modules\ACP3\Polls\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Polls\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\PollRepository
     */
    protected $pollRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\AnswerRepository
     */
    protected $answerRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Validator
     */
    protected $pollsValidator;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\VoteRepository
     */
    protected $voteRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext      $context
     * @param \ACP3\Core\Date                                 $date
     * @param \ACP3\Core\Helpers\FormToken                    $formTokenHelper
     * @param \ACP3\Modules\ACP3\Polls\Model\PollRepository   $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\AnswerRepository $answerRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\VoteRepository   $voteRepository
     * @param \ACP3\Modules\ACP3\Polls\Validator              $pollsValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Polls\Model\PollRepository $pollRepository,
        Polls\Model\AnswerRepository $answerRepository,
        Polls\Model\VoteRepository $voteRepository,
        Polls\Validator $pollsValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
        $this->voteRepository = $voteRepository;
        $this->pollsValidator = $pollsValidator;
    }

    public function actionCreate()
    {
        if ($this->request->getPost()->has('submit')) {
            $this->_createPost($this->request->getPost()->getAll());
        }

        if ($this->request->getPost()->has('add_answer')) {
            $answers = $this->addNewAnswer($this->request->getPost()->get('answers', []));
        } else {
            $answers = [
                ['text' => ''],
                ['text' => '']
            ];
        }

        $defaults = [
            'title' => '',
            'start' => '',
            'end' => ''
        ];

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));
        $this->view->assign('answers', $answers);

        $options = [];
        $options[] = $this->fetchMultipleChoiceOption(0);
        $this->view->assign('options', $options);

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->pollRepository->delete($item);
                }

                return $bool !== false;
            }
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $poll = $this->pollRepository->getOneById($id);

        if (empty($poll) === false) {
            $this->breadcrumb->setTitlePostfix($poll['title']);

            if ($this->request->getPost()->has('submit')) {
                $this->_editPost($this->request->getPost()->getAll(), $id);
            }

            if ($this->request->getPost()->has('add_answer')) {
                $answers = $this->addNewAnswer($this->request->getPost()->get('answers', []));
            } else {
                $answers = $this->answerRepository->getAnswersWithVotesByPollId($id);
            }
            $this->view->assign('answers', $answers);

            $options = [
                $this->fetchMultipleChoiceOption($poll['multiple']),
                [
                    'name' => 'reset',
                    'checked' => $this->get('core.helpers.forms')->selectEntry('reset', '1', '0', 'checked'),
                    'lang' => $this->lang->t('polls', 'reset_votes')
                ]
            ];

            $this->view->assign('options', $options);

            $this->view->assign('form', array_merge($poll, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $polls = $this->pollRepository->getAllInAcp();
        $c_polls = count($polls);

        if ($c_polls > 0) {
            $canDelete = $this->acl->hasPermission('admin/polls/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->user->getEntriesPerPage()
            ];
            $this->view->assign('datatable_config', $config);

            $this->view->assign('polls', $polls);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    /**
     * @param array $formData
     */
    protected function _createPost(array $formData)
    {
        $this->actionHelper->handleCreatePostAction(function () use ($formData) {
            $this->pollsValidator->validate($formData);

            $insertValues = [
                'id' => '',
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'multiple' => isset($formData['multiple']) ? '1' : '0',
                'user_id' => $this->user->getUserId(),
            ];

            $pollId = $this->pollRepository->insert($insertValues);
            $bool2 = false;

            if ($pollId !== false) {
                $bool2 = $this->saveAnswers($formData['answers'], $pollId);
            }

            $this->formTokenHelper->unsetFormToken();

            return $pollId !== false && $bool2 !== false;
        });
    }

    /**
     * @param array $formData
     * @param int   $id
     */
    protected function _editPost(array $formData, $id)
    {
        $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->pollsValidator->validate($formData);

            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'multiple' => isset($formData['multiple']) ? '1' : '0',
                'user_id' => $this->user->getUserId(),
            ];

            $bool = $this->pollRepository->update($updateValues, $id);

            // Reset votes
            if (!empty($formData['reset'])) {
                $this->voteRepository->delete($id, 'poll_id');
            }

            $bool2 = $this->saveAnswers($formData['answers'], $id);

            $this->formTokenHelper->unsetFormToken();

            return $bool !== false && $bool2 !== false;
        });
    }

    /**
     * @param array $currentAnswers
     *
     * @return array
     */
    private function addNewAnswer(array $currentAnswers)
    {
        $answers = [];

        // Bisherige Antworten
        $i = 0;
        foreach ($currentAnswers as $row) {
            if (isset($row['id'])) {
                $answers[$i]['id'] = $row['id'];
            }
            $answers[$i]['text'] = $row['text'];
            ++$i;
        }

        // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
        if (!empty($currentAnswers[$i - 1]['text'])) {
            $answers[$i]['text'] = '';
        }

        return $answers;
    }

    /**
     * @param array $answers
     * @param int   $id
     *
     * @return bool|int
     */
    private function saveAnswers(array $answers, $id)
    {
        $bool = false;
        foreach ($answers as $row) {
            // Neue Antwort hinzufügen
            if (empty($row['id'])) {
                // Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
                if (!empty($row['text']) && !isset($row['delete'])) {
                    $bool = $this->answerRepository->insert(
                        ['text' => Core\Functions::strEncode($row['text']), 'poll_id' => $id]
                    );
                }
            } elseif (isset($row['delete'])) { // Antwort mitsamt Stimmen löschen
                $this->answerRepository->delete((int) $row['id']);
                $this->voteRepository->delete((int) $row['id'], 'answer_id');
            } elseif (!empty($row['text'])) { // Antwort aktualisieren
                $bool = $this->answerRepository->update(
                    ['text' => Core\Functions::strEncode($row['text'])],
                    (int) $row['id']
                );
            }
        }

        return $bool;
    }

    /**
     * @param string $currentValue
     *
     * @return array
     */
    private function fetchMultipleChoiceOption($currentValue)
    {
        return [
            'name' => 'multiple',
            'checked' => $this->get('core.helpers.forms')->selectEntry('multiple', '1', $currentValue, 'checked'),
            'lang' => $this->lang->t('polls', 'multiple_choice')
        ];
    }
}
