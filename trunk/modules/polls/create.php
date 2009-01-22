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

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!validate::date($form['start'], $form['end']))
		$errors[] = $lang->t('common', 'select_date');
	if (empty($form['question']))
		$errors[] = $lang->t('polls', 'type_in_question');
	$i = 0;
	foreach ($form['answers'] as $row) {
		if (!empty($row))
			$i++;
	}
	if ($i <= 1)
		$errors[] = $lang->t('polls', 'type_in_answer');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$start = $date->timestamp($form['start']);
		$end = $date->timestamp($form['end']);
		$question = $db->escape($form['question']);

		$insert_values = array(
			'id' => '',
			'start' => $start,
			'end' => $end,
			'question' => $question,
			'multiple' => isset($form['multiple']) ? '1' : '0',
		);

		$bool = $db->insert('poll_question', $insert_values);

		if ($bool) {
			$poll_id = $db->select('id', 'poll_question', 'start = \'' . $start . '\' AND end = \'' . $end . '\' AND question = \'' . $question . '\'', 'id DESC', 1);
			foreach ($form['answers'] as $row) {
				if (!empty($row)) {
					$insert_answer = array(
						'id' => '',
						'text' => $db->escape($row),
						'poll_id' => $poll_id[0]['id'],
					);
					$bool2 = $db->insert('poll_answers', $insert_answer);
				}
			}
		}

		$content = comboBox($bool && $bool2 ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/polls'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	if (isset($_POST['form']['answers'])) {
		// Bisherige Antworten
		$i = 0;
		foreach ($_POST['form']['answers'] as $row) {
			$answers[$i]['number'] = $i;
			$answers[$i]['value'] = $row;
			$i++;
		}
		// Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
		if (count($_POST['form']['answers']) <= 9 && !empty($_POST['form']['answers'][$i - 1]) && !isset($_POST['submit'])) {
			$answers[$i]['number'] = $i;
			$answers[$i]['value'] = '';
		}
	} else {
		$answers[0]['number'] = 0;
		$answers[0]['value'] = '';
	}

	// Übergabe der Daten an Smarty
	$tpl->assign('start_date', datepicker('start'));
	$tpl->assign('end_date', datepicker('end'));
	$tpl->assign('question', isset($_POST['form']['question']) ? $_POST['form']['question'] : '');
	$tpl->assign('answers', $answers);
	$tpl->assign('multiple', selectEntry('multiple', '1', '0', 'checked'));
	$tpl->assign('disable', count($answers) < 10 ? false : true);

	$content = $tpl->fetch('polls/create.html');
}
?>