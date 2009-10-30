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

if (validate::isNumber($uri->id) && $db->countRows('*', 'poll_question', 'id = \'' . $uri->id . '\'') == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (empty($form['question']))
			$errors[] = $lang->t('polls', 'type_in_question');
		$j = 0;
		foreach ($form['answers'] as $row) {
			if (!empty($row['value']))
				$check_answers = true;
			if (isset($row['delete']))
				$j++;
		}
		if (!isset($check_answers))
			$errors[] = $lang->t('polls', 'type_in_answer');
		if (count($form['answers']) - $j < 2)
			$errors[] = $lang->t('polls', 'can_not_delete_all_answers');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			// Frage aktualisieren
			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'question' => $db->escape($form['question']),
				'multiple' => isset($form['multiple']) ? '1' : '0',
			);

			$bool = $db->update('poll_question', $update_values, 'id = \'' . $uri->id . '\'');

			// Stimmen zurücksetzen
			if (!empty($form['reset']))
				$db->delete('poll_votes', 'poll_id = \'' . $uri->id . '\'');

			// Antworten
			foreach ($form['answers'] as $row) {
				// Neue Antwort hinzufügen
				if (empty($row['id'])) {
					// Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
					if (!empty($row['value']) && !isset($row['delete']))
						$db->insert('poll_answers', array('text' => $db->escape($row['value']), 'poll_id' => $uri->id));
				// Antwort mitsamt Stimmen löschen
				} elseif (isset($row['delete']) && validate::isNumber($row['id'])) {
					$db->delete('poll_answers', 'id = \'' . $row['id'] . '\'');
					if (!empty($form['reset']))
						$db->delete('poll_votes', 'answer_id = \'' . $row['id'] . '\'');
				// Antwort aktualisieren
				} elseif (!empty($row['value']) && validate::isNumber($row['id'])) {
					$bool = $db->update('poll_answers', array('text' => $db->escape($row['value'])), 'id = \'' . $row['id'] . '\'');
				}
			}
			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('acp/polls'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		// Neue Antworten hinzufügen
		if (isset($_POST['form']['answers'])) {
			// Bisherige Antworten
			$i = 0;
			foreach ($_POST['form']['answers'] as $row) {
				$answers[$i]['number'] = $i;
				$answers[$i]['id'] = $row['id'];
				$answers[$i]['value'] = $row['value'];
				$i++;
			}
			// Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
			if (count($_POST['form']['answers']) <= 9 && !empty($_POST['form']['answers'][$i - 1]['value']) && !isset($_POST['submit'])) {
				$answers[$i]['number'] = $i;
				$answers[$i]['id'] = '0';
				$answers[$i]['value'] = '';
			}
		} else {
			$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $uri->id . '\'');
			$c_answers = count($answers);

			for ($i = 0; $i < $c_answers; ++$i) {
				$answers[$i]['number'] = $i;
				$answers[$i]['id'] = $answers[$i]['id'];
				$answers[$i]['value'] = $answers[$i]['text'];
			}
		}
		$poll = $db->select('start, end, question, multiple', 'poll_question', 'id = \'' . $uri->id . '\'');

		$options[0]['name'] = 'reset';
		$options[0]['checked'] = selectEntry('reset', '1', '0', 'checked');
		$options[0]['lang'] = $lang->t('polls', 'reset_votes');
		$options[1]['name'] = 'multiple';
		$options[1]['checked'] = selectEntry('multiple', '1', $poll[0]['multiple'], 'checked');
		$options[1]['lang'] = $lang->t('polls', 'multiple_choice');

		// Übergabe der Daten an Smarty
		$tpl->assign('start_date', datepicker('start', $poll[0]['start']));
		$tpl->assign('end_date', datepicker('end', $poll[0]['end']));
		$tpl->assign('question', isset($form['question']) ? $form['question'] : $poll[0]['question']);
		$tpl->assign('options', $options);
		$tpl->assign('answers', $answers);
		$tpl->assign('disable', count($answers) < 10 ? false : true);

		$content = $tpl->fetch('polls/edit.html');
	}
} else {
	redirect('errors/404');
}
