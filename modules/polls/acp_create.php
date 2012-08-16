<?php
/**
 * Polls
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = $lang->t('common', 'select_date');
	if (empty($_POST['question']))
		$errors['question'] = $lang->t('polls', 'type_in_question');
	$i = 0;
	foreach ($_POST['answers'] as $row) {
		if (!empty($row))
			++$i;
	}
	if ($i <= 1)
		$errors[] = $lang->t('polls', 'type_in_answer');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$start = $_POST['start'];
		$end = $_POST['end'];
		$question = $db->escape($_POST['question']);

		$insert_values = array(
			'id' => '',
			'start' => $start,
			'end' => $end,
			'question' => $question,
			'multiple' => isset($_POST['multiple']) ? '1' : '0',
			'user_id' => $auth->getUserId(),
		);

		$bool = $db->insert('polls', $insert_values);
		$bool2 = false;

		if ($bool !== false) {
			$poll_id = $db->select('id', 'polls', 'start = \'' . $start . '\' AND end = \'' . $end . '\' AND question = \'' . $question . '\'', 'id DESC', 1);
			foreach ($_POST['answers'] as $row) {
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

		$session->unsetFormToken();

		setRedirectMessage($bool && $bool2, $bool !== false && $bool2 !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), 'acp/polls');
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
	$tpl->assign('publication_period', $date->datepicker(array('start', 'end')));
	$tpl->assign('question', isset($_POST['question']) ? $_POST['question'] : '');
	$tpl->assign('answers', $answers);
	$tpl->assign('multiple', selectEntry('multiple', '1', '0', 'checked'));
	$tpl->assign('disable', count($answers) < 10 ? false : true);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('polls/acp_create.tpl'));
}
