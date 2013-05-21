<?php

namespace ACP3\Modules\Polls;

use ACP3\Core;

/**
 * Description of PollsAdmin
 *
 * @author Tino
 */
class PollsAdmin extends Core\ModuleController {

	public function actionCreate() {
		if (isset($_POST['submit']) === true) {
			if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
				$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
			if (empty($_POST['title']))
				$errors['title'] = Core\Registry::get('Lang')->t('polls', 'type_in_question');
			$i = 0;
			foreach ($_POST['answers'] as $row) {
				if (!empty($row))
					++$i;
			}
			if ($i <= 1)
				$errors[] = Core\Registry::get('Lang')->t('polls', 'type_in_answer');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$insert_values = array(
					'id' => '',
					'start' => Core\Registry::get('Date')->toSQL($_POST['start']),
					'end' => Core\Registry::get('Date')->toSQL($_POST['end']),
					'title' => Core\Functions::strEncode($_POST['title']),
					'multiple' => isset($_POST['multiple']) ? '1' : '0',
					'user_id' => Core\Registry::get('Auth')->getUserId(),
				);

				$bool = Core\Registry::get('Db')->insert(DB_PRE . 'polls', $insert_values);
				$poll_id = Core\Registry::get('Db')->lastInsertId();
				$bool2 = false;

				if ($bool !== false) {
					foreach ($_POST['answers'] as $row) {
						if (!empty($row)) {
							$insert_answer = array(
								'id' => '',
								'text' => Core\Functions::strEncode($row),
								'poll_id' => $poll_id,
							);
							$bool2 = Core\Registry::get('Db')->insert(DB_PRE . 'poll_answers', $insert_answer);
						}
					}
				}

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool && $bool2, Core\Registry::get('Lang')->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/polls');
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
			Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end')));
			Core\Registry::get('View')->assign('title', isset($_POST['title']) ? $_POST['title'] : '');
			Core\Registry::get('View')->assign('answers', $answers);
			Core\Registry::get('View')->assign('multiple', Core\Functions::selectEntry('multiple', '1', '0', 'checked'));
			Core\Registry::get('View')->assign('disable', count($answers) < 10 ? false : true);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionDelete() {
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/polls/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/polls')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = $bool2 = $bool3 = false;
			foreach ($marked_entries as $entry) {
				$bool = Core\Registry::get('Db')->delete(DB_PRE . 'polls', array('id' => $entry));
				$bool2 = Core\Registry::get('Db')->delete(DB_PRE . 'poll_answers', array('poll_id' => $entry));
				$bool3 = Core\Registry::get('Db')->delete(DB_PRE . 'poll_votes', array('poll_id' => $entry));
			}
			Core\Functions::setRedirectMessage($bool && $bool2 && $bool3, Core\Registry::get('Lang')->t('system', $bool !== false && $bool2 !== false && $bool3 !== false ? 'delete_success' : 'delete_error'), 'acp/polls');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			if (isset($_POST['submit']) === true) {
				if (Core\Validate::date($_POST['start'], $_POST['end']) === false)
					$errors[] = Core\Registry::get('Lang')->t('system', 'select_date');
				if (empty($_POST['title']))
					$errors['title'] = Core\Registry::get('Lang')->t('polls', 'type_in_question');
				$j = 0;
				foreach ($_POST['answers'] as $row) {
					if (!empty($row['value']))
						$check_answers = true;
					if (isset($row['delete']))
						++$j;
				}
				if (!isset($check_answers))
					$errors[] = Core\Registry::get('Lang')->t('polls', 'type_in_answer');
				if (count($_POST['answers']) - $j < 2)
					$errors[] = Core\Registry::get('Lang')->t('polls', 'can_not_delete_all_answers');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					// Frage aktualisieren
					$update_values = array(
						'start' => Core\Registry::get('Date')->toSQL($_POST['start']),
						'end' => Core\Registry::get('Date')->toSQL($_POST['end']),
						'title' => Core\Functions::strEncode($_POST['title']),
						'multiple' => isset($_POST['multiple']) ? '1' : '0',
						'user_id' => Core\Registry::get('Auth')->getUserId(),
					);

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'polls', $update_values, array('id' => Core\Registry::get('URI')->id));

					// Stimmen zurücksetzen
					if (!empty($_POST['reset']))
						Core\Registry::get('Db')->delete(DB_PRE . 'poll_votes', array('poll_id' => Core\Registry::get('URI')->id));

					// Antworten
					foreach ($_POST['answers'] as $row) {
						// Neue Antwort hinzufügen
						if (empty($row['id'])) {
							// Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
							if (!empty($row['value']) && !isset($row['delete']))
								Core\Registry::get('Db')->insert(DB_PRE . 'poll_answers', array('text' => Core\Functions::strEncode($row['value']), 'poll_id' => Core\Registry::get('URI')->id));
							// Antwort mitsamt Stimmen löschen
						} elseif (isset($row['delete']) && Core\Validate::isNumber($row['id'])) {
							Core\Registry::get('Db')->delete(DB_PRE . 'poll_answers', array('id' => $row['id']));
							if (!empty($_POST['reset']))
								Core\Registry::get('Db')->delete(DB_PRE . 'poll_votes', array('answer_id' => $row['id']));
							// Antwort aktualisieren
						} elseif (!empty($row['value']) && Core\Validate::isNumber($row['id'])) {
							$bool = Core\Registry::get('Db')->update(DB_PRE . 'poll_answers', array('text' => Core\Functions::strEncode($row['value'])), array('id' => $row['id']));
						}
					}

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/polls');
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
					$answers = Core\Registry::get('Db')->fetchAll('SELECT id, text FROM ' . DB_PRE . 'poll_answers WHERE poll_id = ?', array(Core\Registry::get('URI')->id));
					$c_answers = count($answers);

					for ($i = 0; $i < $c_answers; ++$i) {
						$answers[$i]['number'] = $i;
						$answers[$i]['id'] = $answers[$i]['id'];
						$answers[$i]['value'] = $answers[$i]['text'];
					}
				}
				Core\Registry::get('View')->assign('answers', $answers);

				$poll = Core\Registry::get('Db')->fetchAssoc('SELECT start, end, title, multiple FROM ' . DB_PRE . 'polls WHERE id = ?', array(Core\Registry::get('URI')->id));

				$options = array();
				$options[0]['name'] = 'reset';
				$options[0]['checked'] = Core\Functions::selectEntry('reset', '1', '0', 'checked');
				$options[0]['lang'] = Core\Registry::get('Lang')->t('polls', 'reset_votes');
				$options[1]['name'] = 'multiple';
				$options[1]['checked'] = Core\Functions::selectEntry('multiple', '1', $poll['multiple'], 'checked');
				$options[1]['lang'] = Core\Registry::get('Lang')->t('polls', 'multiple_choice');
				Core\Registry::get('View')->assign('options', $options);

				// Übergabe der Daten an Smarty
				Core\Registry::get('View')->assign('publication_period', Core\Registry::get('Date')->datepicker(array('start', 'end'), array($poll['start'], $poll['end'])));
				Core\Registry::get('View')->assign('title', isset($_POST['title']) ? $_POST['title'] : $poll['title']);
				Core\Registry::get('View')->assign('disable', count($answers) < 10 ? false : true);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionList() {
		Core\Functions::getRedirectMessage();

		$polls = Core\Registry::get('Db')->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'polls ORDER BY start DESC, end DESC, id DESC');
		$c_polls = count($polls);

		if ($c_polls > 0) {
			$can_delete = Core\Modules::hasPermission('polls', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'desc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));

			for ($i = 0; $i < $c_polls; ++$i) {
				$polls[$i]['period'] = Core\Registry::get('Date')->formatTimeRange($polls[$i]['start'], $polls[$i]['end']);
			}
			Core\Registry::get('View')->assign('polls', $polls);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
		}
	}

}