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
		$insert_values = array(
			'id' => '',
			'start' => $date->timestamp($form['start']),
			'end' => $date->timestamp($form['end']),
			'question' => $db->escape($form['question']),
		);

		$bool = $db->insert('poll_question', $insert_values);

		if ($bool) {
			$poll_id = $db->select('id', 'poll_question', 'start = \'' . $date->timestamp($form['start']) . '\' AND end = \'' . $date->timestamp($form['end']) . '\' AND question = \'' . $db->escape($form['question']) . '\'', 'id DESC', 1);
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

		$content = comboBox($bool && $bool2 ? $lang->t('polls', 'create_success') : $lang->t('polls', 'create_error'), uri('acp/polls'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', datepicker('start'));
	$tpl->assign('end_date', datepicker('end'));

	$tpl->assign('disable', false);
	if (isset($_POST['form']['answers'])) {
		$i = 0;
		foreach ($_POST['form']['answers'] as $row) {
			$answers[$i]['number'] = $i + 1;
			$answers[$i]['value'] = $row;
			$i++;
		}
		if (count($_POST['form']['answers']) <= 9 && !isset($_POST['submit'])) {
			$answers[$i]['number'] = $i + 1;
			$answers[$i]['value'] = '';
		}
		if (count($_POST['form']['answers']) >= 9) {
			$tpl->assign('disable', true);
		}
	} else {
		$answers[0]['number'] = 1;
		$answers[0]['value'] = '';
	}
	$tpl->assign('answers', $answers);

	$tpl->assign('question', isset($_POST['form']['question']) ? $_POST['form']['question'] : '');

	$content = $tpl->fetch('polls/create.html');
}
?>