<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check('polls', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];

		if (!$validate->date($form))
			$errors[] = lang('common', 'select_date');
		if (empty($form['question']))
			$errors[] = lang('polls', 'type_in_question');
		foreach ($form['answers'] as $row) {
			if (!empty($row)) {
				$check_answers = true;
				break;
			}
		}
		if (!isset($check_answers))
			$errors[] = lang('polls', 'type_in_answer');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$insert_values = array(
				'id' => '',
				'start' => $start_date,
				'end' => $end_date,
				'question' => $db->escape($form['question']),
			);

			$bool = $db->insert('poll_question', $insert_values);

			if ($bool) {
				$poll_id = $db->select('id', 'poll_question', 'start = \'' . $start_date . '\' AND end = \'' . $end_date . '\' AND question = \'' . $db->escape($form['question']) . '\'', 'id DESC', 1);
				foreach ($form['answers'] as $row) {
					$insert_answer = array(
						'id' => '',
						'text' => $db->escape($row),
						'poll_id' => $poll_id[0]['id'],
					);
					if (!empty($row) && $bool2 = $db->insert('poll_answers', $insert_answer))
						continue;
				}
			}

			$content = combo_box($bool && $bool2 ? lang('polls', 'create_success') : lang('polls', 'create_error'), uri('acp/polls'));
		}
		break;
	case 'edit':
		$form = $_POST['form'];

		if (!$validate->date($form))
			$errors[] = lang('common', 'select_date');
		if (empty($form['question']))
			$errors[] = lang('polls', 'type_in_question');
		$j = 0;
		foreach ($form['answers'] as $row) {
			if (!empty($row['value']))
				$check_answers = true;
			if (isset($row['delete']))
				$j++;
		}
		if (!isset($check_answers))
			$errors[] = lang('polls', 'type_in_answer');
		if ($j == count($form['answers']))
			$errors[] = lang('polls', 'can_not_delete_all_answers');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'question' => $db->escape($form['question']),
			);

			$bool = $db->update('poll_question', $update_values, 'id = \'' . $modules->id . '\'');

			foreach ($form['answers'] as $row) {
				if (isset($row['delete']) && $validate->is_number($row['id'])) {
					$db->delete('poll_answers', 'id = \'' . $row['id'] . '\'');
					$db->delete('poll_votes', 'answer_id = \'' . $row['id'] . '\'');
				} elseif ($validate->is_number($row['id'])) {
					$bool = $db->update('poll_answers', array('text' =>$db->escape($row['value'])), 'id = \'' . $db->escape($row['id']) . '\'');
				}
			}
			$content = combo_box($bool ? lang('polls', 'edit_success') : lang('polls', 'edit_error'), uri('acp/polls'));
		}
		break;
	case 'delete':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('polls', 'confirm_delete'), uri('acp/polls/adm_list/action_delete/entries_' . $marked_entries), uri('acp/polls'));
		} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'poll_question', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					$bool = $db->delete('poll_question', 'id = \'' . $entry . '\'');
					$bool2 = $db->delete('poll_answers', 'poll_id = \'' . $entry . '\'');
					$bool3 = $db->delete('poll_votes', 'poll_id = \'' . $entry . '\'');
				}
			}
			$content = combo_box($bool && $bool2 && $bool3 ? lang('polls', 'delete_success') : lang('polls', 'delete_error'), uri('acp/polls'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>