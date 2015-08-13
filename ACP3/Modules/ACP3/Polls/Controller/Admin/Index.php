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

        $answers = [];
        if ($this->request->getPost()->has('answers')) {
            // Bisherige Antworten
            $i = 0;
            $answersPost = $this->request->getPost()->get('answers', []);
            foreach ($answersPost as $row) {
                $answers[$i]['number'] = $i;
                $answers[$i]['value'] = $row;
                ++$i;
            }
            // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
            if (!empty($answersPost[$i - 1])) {
                $answers[$i]['number'] = $i;
                $answers[$i]['value'] = '';
            }
        } else {
            $answers[0]['number'] = 0;
            $answers[0]['value'] = '';
            $answers[1]['number'] = 1;
            $answers[1]['value'] = '';
        }

        $defaults = [
            'title' => '',
            'start' => '',
            'end' => ''
        ];

        $this->view->assign('form', array_merge($defaults, $this->request->getPost()->getAll()));
        $this->view->assign('answers', $answers);
        $this->view->assign('multiple', $this->get('core.helpers.forms')->selectEntry('multiple', '1', '0', 'checked'));

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

            $answers = [];
            // Neue Antworten hinzufügen
            if ($this->request->getPost()->has('answers')) {
                // Bisherige Antworten
                $i = 0;
                $answersPost = $this->request->getPost()->get('answers', []);
                foreach ($answersPost as $row) {
                    $answers[$i]['number'] = $i;
                    $answers[$i]['id'] = $row['id'];
                    $answers[$i]['value'] = $row['value'];
                    ++$i;
                }
                // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
                if (!empty($answersPost[$i - 1]['value'])) {
                    $answers[$i]['number'] = $i;
                    $answers[$i]['id'] = '0';
                    $answers[$i]['value'] = '';
                }
            } else {
                $answers = $this->pollsModel->getAnswersByPollId($id);
                $c_answers = count($answers);

                for ($i = 0; $i < $c_answers; ++$i) {
                    $answers[$i]['number'] = $i;
                    $answers[$i]['value'] = $answers[$i]['text'];
                }
            }
            $this->view->assign('answers', $answers);

            $options = [];
            $options[0]['name'] = 'reset';
            $options[0]['checked'] = $this->get('core.helpers.forms')->selectEntry('reset', '1', '0', 'checked');
            $options[0]['lang'] = $this->lang->t('polls', 'reset_votes');
            $options[1]['name'] = 'multiple';
            $options[1]['checked'] = $this->get('core.helpers.forms')->selectEntry('multiple', '1', $poll['multiple'], 'checked');
            $options[1]['lang'] = $this->lang->t('polls', 'multiple_choice');
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
            $this->pollsValidator->validateCreate($formData);

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
                foreach ($formData['answers'] as $row) {
                    if (!empty($row)) {
                        $insertAnswer = [
                            'id' => '',
                            'text' => Core\Functions::strEncode($row),
                            'poll_id' => $pollId,
                        ];
                        $bool2 = $this->pollsModel->insert($insertAnswer, Polls\Model::TABLE_NAME_ANSWERS);
                    }
                }
            }

            $this->formTokenHelper->unsetFormToken();

            return $pollId && $bool2;
        });
    }

    /**
     * @param array $formData
     * @param int   $id
     */
    protected function _editPost(array $formData, $id)
    {
        $this->actionHelper->handleEditPostAction(function () use ($formData, $id) {
            $this->pollsValidator->validateEdit($formData);

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

            foreach ($formData['answers'] as $row) {
                // Neue Antwort hinzufügen
                if (empty($row['id'])) {
                    // Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
                    if (!empty($row['value']) && !isset($row['delete'])) {
                        $this->pollsModel->insert(
                            ['text' => Core\Functions::strEncode($row['value']), 'poll_id' => $id],
                            Polls\Model::TABLE_NAME_ANSWERS
                        );
                    }
                } elseif (isset($row['delete']) && $this->get('core.validator.rules.misc')->isNumber($row['id'])) { // Antwort mitsamt Stimmen löschen
                    $this->pollsModel->delete($row['id'], '', Polls\Model::TABLE_NAME_ANSWERS);
                    $this->pollsModel->delete($row['id'], 'answer_id', Polls\Model::TABLE_NAME_VOTES);
                } elseif (!empty($row['value']) && $this->get('core.validator.rules.misc')->isNumber($row['id'])) { // Antwort aktualisieren
                    $bool = $this->pollsModel->update(
                        ['text' => Core\Functions::strEncode($row['value'])],
                        $row['id'],
                        Polls\Model::TABLE_NAME_ANSWERS
                    );
                }
            }

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
