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
     * @var Polls\Model
     */
    protected $pollsModel;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Validator
     */
    protected $pollsValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Date                            $date
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Polls\Model             $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Validator         $pollsValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Polls\Model $pollsModel,
        Polls\Validator $pollsValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->pollsModel = $pollsModel;
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
                ['number' => 0, 'value' => ''],
                ['number' => 1, 'value' => '']
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
                $bool = $bool2 = $bool3 = false;
                foreach ($items as $item) {
                    $bool = $this->pollsModel->delete($item);
                    $bool2 = $this->pollsModel->delete($item, 'poll_id', Polls\Model::TABLE_NAME_ANSWERS);
                    $bool3 = $this->pollsModel->delete($item, 'poll_id', Polls\Model::TABLE_NAME_VOTES);
                }

                return $bool !== false && $bool2 !== false && $bool3 !== false;
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
        $poll = $this->pollsModel->getOneById($id);

        if (empty($poll) === false) {
            $this->breadcrumb->setTitlePostfix($poll['title']);

            if ($this->request->getPost()->has('submit')) {
                $this->_editPost($this->request->getPost()->getAll(), $id);
            }

            if ($this->request->getPost()->has('add_answer')) {
                $answers = $this->addNewAnswer($this->request->getPost()->get('answers', []));
            } else {
                $answers = $this->pollsModel->getAnswersByPollId($id);
                $c_answers = count($answers);

                for ($i = 0; $i < $c_answers; ++$i) {
                    $answers[$i]['number'] = $i;
                    $answers[$i]['value'] = $answers[$i]['text'];
                }
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
        $polls = $this->pollsModel->getAllInAcp();
        $c_polls = count($polls);

        if ($c_polls > 0) {
            $canDelete = $this->acl->hasPermission('admin/polls/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
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
                'user_id' => $this->auth->getUserId(),
            ];

            $pollId = $this->pollsModel->insert($insertValues);
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
                'user_id' => $this->auth->getUserId(),
            ];

            $bool = $this->pollsModel->update($updateValues, $id);

            // Stimmen zurücksetzen
            if (!empty($formData['reset'])) {
                $this->pollsModel->delete($id, 'poll_id', Polls\Model::TABLE_NAME_VOTES);
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
            $answers[$i]['number'] = $i;
            if (isset($row['id'])) {
                $answers[$i]['id'] = $row['id'];
            }
            $answers[$i]['value'] = $row['value'];
            ++$i;
        }

        // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
        if (!empty($currentAnswers[$i - 1]['value'])) {
            $answers[$i]['number'] = $i;
            $answers[$i]['value'] = '';
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
                if (!empty($row['value']) && !isset($row['delete'])) {
                    $bool = $this->pollsModel->insert(
                        ['text' => Core\Functions::strEncode($row['value']), 'poll_id' => $id],
                        Polls\Model::TABLE_NAME_ANSWERS
                    );
                }
            } elseif (isset($row['delete'])) { // Antwort mitsamt Stimmen löschen
                $this->pollsModel->delete((int) $row['id'], '', Polls\Model::TABLE_NAME_ANSWERS);
                $this->pollsModel->delete((int) $row['id'], 'answer_id', Polls\Model::TABLE_NAME_VOTES);
            } elseif (!empty($row['value'])) { // Antwort aktualisieren
                $bool = $this->pollsModel->update(
                    ['text' => Core\Functions::strEncode($row['value'])],
                    (int) $row['id'],
                    Polls\Model::TABLE_NAME_ANSWERS
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
