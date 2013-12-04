<?php

namespace ACP3\Modules\Polls\Controller;

use ACP3\Core;
use ACP3\Modules\Polls;

/**
 * Description of PollsAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    public function __construct()
    {
        parent::__construct();
    }

    public function actionCreate()
    {
        if (isset($_POST['submit']) === true) {
            if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
                $errors[] = $this->lang->t('system', 'select_date');
            if (empty($_POST['title']))
                $errors['title'] = $this->lang->t('polls', 'type_in_question');
            $i = 0;
            foreach ($_POST['answers'] as $row) {
                if (!empty($row))
                    ++$i;
            }
            if ($i <= 1)
                $errors[] = $this->lang->t('polls', 'type_in_answer');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } elseif (Core\Validate::formToken() === false) {
                $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
            } else {
                $insert_values = array(
                    'id' => '',
                    'start' => $this->date->toSQL($_POST['start']),
                    'end' => $this->date->toSQL($_POST['end']),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'multiple' => isset($_POST['multiple']) ? '1' : '0',
                    'user_id' => $this->auth->getUserId(),
                );

                $bool = $this->db->insert(DB_PRE . 'polls', $insert_values);
                $poll_id = $this->db->lastInsertId();
                $bool2 = false;

                if ($bool !== false) {
                    foreach ($_POST['answers'] as $row) {
                        if (!empty($row)) {
                            $insert_answer = array(
                                'id' => '',
                                'text' => Core\Functions::strEncode($row),
                                'poll_id' => $poll_id,
                            );
                            $bool2 = $this->db->insert(DB_PRE . 'poll_answers', $insert_answer);
                        }
                    }
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool && $bool2, $this->lang->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/polls');
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            $answers = array();
            if (isset($_POST['answers'])) {
                // Bisherige Antworten
                $i = 0;
                foreach ($_POST['answers'] as $row) {
                    $answers[$i]['number'] = $i;
                    $answers[$i]['value'] = $row;
                    ++$i;
                }
                // Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
                if (count($_POST['answers']) <= 9 && !empty($_POST['answers'][$i - 1]) && isset($_POST['submit']) === false) {
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
            $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end')));
            $this->view->assign('title', isset($_POST['title']) ? $_POST['title'] : '');
            $this->view->assign('answers', $answers);
            $this->view->assign('multiple', Core\Functions::selectEntry('multiple', '1', '0', 'checked'));
            $this->view->assign('disable', count($answers) < 10 ? false : true);

            $this->session->generateFormToken();
        }
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/polls/delete', 'acp/polls');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = $bool2 = $bool3 = false;
            foreach ($items as $item) {
                $bool = $this->db->delete(DB_PRE . 'polls', array('id' => $item));
                $bool2 = $this->db->delete(DB_PRE . 'poll_answers', array('poll_id' => $item));
                $bool3 = $this->db->delete(DB_PRE . 'poll_votes', array('poll_id' => $item));
            }
            Core\Functions::setRedirectMessage($bool && $bool2 && $bool3, $this->lang->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error'), 'acp/polls');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = ?', array($this->uri->id)) == 1
        ) {
            if (isset($_POST['submit']) === true) {
                if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
                    $errors[] = $this->lang->t('system', 'select_date');
                if (empty($_POST['title']))
                    $errors['title'] = $this->lang->t('polls', 'type_in_question');
                $j = 0;
                foreach ($_POST['answers'] as $row) {
                    if (!empty($row['value']))
                        $check_answers = true;
                    if (isset($row['delete']))
                        ++$j;
                }
                if (!isset($check_answers))
                    $errors[] = $this->lang->t('polls', 'type_in_answer');
                if (count($_POST['answers']) - $j < 2)
                    $errors[] = $this->lang->t('polls', 'can_not_delete_all_answers');

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    // Frage aktualisieren
                    $update_values = array(
                        'start' => $this->date->toSQL($_POST['start']),
                        'end' => $this->date->toSQL($_POST['end']),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'multiple' => isset($_POST['multiple']) ? '1' : '0',
                        'user_id' => $this->auth->getUserId(),
                    );

                    $bool = $this->db->update(DB_PRE . 'polls', $update_values, array('id' => $this->uri->id));

                    // Stimmen zurücksetzen
                    if (!empty($_POST['reset']))
                        $this->db->delete(DB_PRE . 'poll_votes', array('poll_id' => $this->uri->id));

                    // Antworten
                    foreach ($_POST['answers'] as $row) {
                        // Neue Antwort hinzufügen
                        if (empty($row['id'])) {
                            // Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
                            if (!empty($row['value']) && !isset($row['delete']))
                                $this->db->insert(DB_PRE . 'poll_answers', array('text' => Core\Functions::strEncode($row['value']), 'poll_id' => $this->uri->id));
                            // Antwort mitsamt Stimmen löschen
                        } elseif (isset($row['delete']) && Core\Validate::isNumber($row['id'])) {
                            $this->db->delete(DB_PRE . 'poll_answers', array('id' => $row['id']));
                            if (!empty($_POST['reset']))
                                $this->db->delete(DB_PRE . 'poll_votes', array('answer_id' => $row['id']));
                            // Antwort aktualisieren
                        } elseif (!empty($row['value']) && Core\Validate::isNumber($row['id'])) {
                            $bool = $this->db->update(DB_PRE . 'poll_answers', array('text' => Core\Functions::strEncode($row['value'])), array('id' => $row['id']));
                        }
                    }

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/polls');
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                $answers = array();
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
                    if (count($_POST['answers']) <= 9 && !empty($_POST['answers'][$i - 1]['value']) && isset($_POST['submit']) === false) {
                        $answers[$i]['number'] = $i;
                        $answers[$i]['id'] = '0';
                        $answers[$i]['value'] = '';
                    }
                } else {
                    $answers = $this->db->fetchAll('SELECT id, text FROM ' . DB_PRE . 'poll_answers WHERE poll_id = ?', array($this->uri->id));
                    $c_answers = count($answers);

                    for ($i = 0; $i < $c_answers; ++$i) {
                        $answers[$i]['number'] = $i;
                        $answers[$i]['id'] = $answers[$i]['id'];
                        $answers[$i]['value'] = $answers[$i]['text'];
                    }
                }
                $this->view->assign('answers', $answers);

                $poll = $this->db->fetchAssoc('SELECT start, end, title, multiple FROM ' . DB_PRE . 'polls WHERE id = ?', array($this->uri->id));

                $options = array();
                $options[0]['name'] = 'reset';
                $options[0]['checked'] = Core\Functions::selectEntry('reset', '1', '0', 'checked');
                $options[0]['lang'] = $this->lang->t('polls', 'reset_votes');
                $options[1]['name'] = 'multiple';
                $options[1]['checked'] = Core\Functions::selectEntry('multiple', '1', $poll['multiple'], 'checked');
                $options[1]['lang'] = $this->lang->t('polls', 'multiple_choice');
                $this->view->assign('options', $options);

                // Übergabe der Daten an Smarty
                $this->view->assign('publication_period', $this->date->datepicker(array('start', 'end'), array($poll['start'], $poll['end'])));
                $this->view->assign('title', isset($_POST['title']) ? $_POST['title'] : $poll['title']);
                $this->view->assign('disable', count($answers) < 10 ? false : true);

                $this->session->generateFormToken();
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $polls = $this->db->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'polls ORDER BY start DESC, end DESC, id DESC');
        $c_polls = count($polls);

        if ($c_polls > 0) {
            $can_delete = Core\Modules::hasPermission('polls', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::datatable($config));

            for ($i = 0; $i < $c_polls; ++$i) {
                $polls[$i]['period'] = $this->date->formatTimeRange($polls[$i]['start'], $polls[$i]['end']);
            }
            $this->view->assign('polls', $polls);
            $this->view->assign('can_delete', $can_delete);
        }
    }

}