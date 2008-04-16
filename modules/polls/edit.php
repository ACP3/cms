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

if (validate::isNumber($modules->id) && $db->select('id', 'poll_question', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start']) || !validate::date($form['end']))
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
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$start_date = strtotime($form['start'], dateAligned(2, time()));
			$end_date = strtotime($form['end'], dateAligned(2, time()));

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'question' => $db->escape($form['question']),
			);

			$bool = $db->update('poll_question', $update_values, 'id = \'' . $modules->id . '\'');

			foreach ($form['answers'] as $row) {
				if (isset($row['delete']) && validate::isNumber($row['id'])) {
					$db->delete('poll_answers', 'id = \'' . $row['id'] . '\'');
					$db->delete('poll_votes', 'answer_id = \'' . $row['id'] . '\'');
				} elseif (validate::isNumber($row['id'])) {
					$bool = $db->update('poll_answers', array('text' =>$db->escape($row['value'])), 'id = \'' . $db->escape($row['id']) . '\'');
				}
			}
			$content = comboBox($bool ? lang('polls', 'edit_success') : lang('polls', 'edit_error'), uri('acp/polls'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$poll = $db->select('start, end, question', 'poll_question', 'id = \'' . $modules->id . '\'');

		// Datumsauswahl
		$tpl->assign('start_date', datepicker('start', $poll[0]['start']));
		$tpl->assign('end_date', datepicker('end', $poll[0]['end']));

		$tpl->assign('question', isset($form['question']) ? $form['question'] : $poll[0]['question']);

		$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $modules->id . '\'');
		$c_answers = count($answers);

		for ($i = 0; $i < $c_answers; $i++) {
			$answers[$i]['number'] = $i + 1;
			$answers[$i]['id'] = $answers[$i]['id'];
			$answers[$i]['value'] = $answers[$i]['text'];
		}
		$tpl->assign('answers', $answers);

		$content = $tpl->fetch('polls/edit.html');
	}
} else {
	redirect('errors/404');
}
?>