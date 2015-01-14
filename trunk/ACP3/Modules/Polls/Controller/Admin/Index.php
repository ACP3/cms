<?php

namespace ACP3\Modules\Polls\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Polls;

/**
 * Class Index
 * @package ACP3\Modules\Polls\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Polls\Model
     */
    protected $pollsModel;
    /**
     * @var \ACP3\Modules\Polls\Validator
     */
    protected $pollsValidator;

    /**
     * @param \ACP3\Core\Context\Admin      $context
     * @param \ACP3\Core\Date               $date
     * @param \ACP3\Core\Helpers\Secure     $secureHelper
     * @param \ACP3\Modules\Polls\Model     $pollsModel
     * @param \ACP3\Modules\Polls\Validator $pollsValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Polls\Model $pollsModel,
        Polls\Validator $pollsValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->pollsModel = $pollsModel;
        $this->pollsValidator = $pollsValidator;
    }

    public function actionCreate()
    {
        if (isset($_POST['submit']) === true) {
            $this->_createPost($_POST);
        }

        $answers = [];
        if (isset($_POST['answers'])) {
            // Bisherige Antworten
            $i = 0;
            foreach ($_POST['answers'] as $row) {
                $answers[$i]['number'] = $i;
                $answers[$i]['value'] = $row;
                ++$i;
            }
            // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
            if (!empty($_POST['answers'][$i - 1])) {
                $answers[$i]['number'] = $i;
                $answers[$i]['value'] = '';
            }
        } else {
            $answers[0]['number'] = 0;
            $answers[0]['value'] = '';
            $answers[1]['number'] = 1;
            $answers[1]['value'] = '';
        }

        // Übergabe der Daten an Smarty
        $this->view->assign('publication_period', $this->date->datepicker(['start', 'end']));
        $this->view->assign('title', isset($_POST['title']) ? $_POST['title'] : '');
        $this->view->assign('answers', $answers);
        $this->view->assign('multiple', $this->get('core.helpers.forms')->selectEntry('multiple', '1', '0', 'checked'));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/polls/index/delete', 'acp/polls');

        if ($this->request->action === 'confirmed') {
            $bool = $bool2 = $bool3 = false;
            foreach ($items as $item) {
                $bool = $this->pollsModel->delete($item);
                $bool2 = $this->pollsModel->delete($item, 'poll_id', Polls\Model::TABLE_NAME_ANSWERS);
                $bool3 = $this->pollsModel->delete($item, 'poll_id', Polls\Model::TABLE_NAME_VOTES);
            }

            $this->redirectMessages()->setMessage($bool !== false && $bool2 !== false && $bool3 !== false, $this->lang->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error'), 'acp/polls');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $poll = $this->pollsModel->getOneById($this->request->id);

        if (empty($poll) === false) {
            $this->breadcrumb->setTitlePostfix($poll['title']);

            if (isset($_POST['submit']) === true) {
                $this->_editPost($_POST);
            }

            $answers = [];
            // Neue Antworten hinzufügen
            if (isset($_POST['answers'])) {
                // Bisherige Antworten
                $i = 0;
                foreach ($_POST['answers'] as $row) {
                    $answers[$i]['number'] = $i;
                    $answers[$i]['id'] = $row['id'];
                    $answers[$i]['value'] = $row['value'];
                    ++$i;
                }
                // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
                if (!empty($_POST['answers'][$i - 1]['value'])) {
                    $answers[$i]['number'] = $i;
                    $answers[$i]['id'] = '0';
                    $answers[$i]['value'] = '';
                }
            } else {
                $answers = $this->pollsModel->getAnswersByPollId($this->request->id);
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

            // Übergabe der Daten an Smarty
            $this->view->assign('publication_period', $this->date->datepicker(['start', 'end'], [$poll['start'], $poll['end']]));
            $this->view->assign('title', isset($_POST['title']) ? $_POST['title'] : $poll['title']);

            $this->secureHelper->generateFormToken($this->request->query);
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
    private function _createPost(array $formData)
    {
        try {
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

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($pollId && $bool2, $this->lang->t('system', $pollId !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/polls');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/polls');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    private function _editPost(array $formData)
    {
        try {
            $this->pollsValidator->validateEdit($formData);

            // Frage aktualisieren
            $updateValues = [
                'start' => $this->date->toSQL($formData['start']),
                'end' => $this->date->toSQL($formData['end']),
                'title' => Core\Functions::strEncode($formData['title']),
                'multiple' => isset($formData['multiple']) ? '1' : '0',
                'user_id' => $this->auth->getUserId(),
            ];

            $bool = $this->pollsModel->update($updateValues, $this->request->id);

            // Stimmen zurücksetzen
            if (!empty($formData['reset'])) {
                $this->pollsModel->delete($this->request->id, 'poll_id', Polls\Model::TABLE_NAME_VOTES);
            }

            // Antworten
            foreach ($formData['answers'] as $row) {
                // Neue Antwort hinzufügen
                if (empty($row['id'])) {
                    // Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
                    if (!empty($row['value']) && !isset($row['delete'])) {
                        $this->pollsModel->insert(['text' => Core\Functions::strEncode($row['value']), 'poll_id' => $this->request->id], Polls\Model::TABLE_NAME_ANSWERS);
                    }
                    // Antwort mitsamt Stimmen löschen
                } elseif (isset($row['delete']) && $this->get('core.validator.rules.misc')->isNumber($row['id'])) {
                    $this->pollsModel->delete($row['id'], '', Polls\Model::TABLE_NAME_ANSWERS);
                    $this->pollsModel->delete($row['id'], 'answer_id', Polls\Model::TABLE_NAME_VOTES);
                    // Antwort aktualisieren
                } elseif (!empty($row['value']) && $this->get('core.validator.rules.misc')->isNumber($row['id'])) {
                    $bool = $this->pollsModel->update(['text' => Core\Functions::strEncode($row['value'])], $row['id'], Polls\Model::TABLE_NAME_ANSWERS);
                }
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/polls');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/polls');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
