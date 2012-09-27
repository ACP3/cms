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
		$errors[] = ACP3_CMS::$lang->t('system', 'select_date');
	if (empty($_POST['question']))
		$errors['question'] = ACP3_CMS::$lang->t('polls', 'type_in_question');
	$i = 0;
	foreach ($_POST['answers'] as $row) {
		if (!empty($row))
			++$i;
	}
	if ($i <= 1)
		$errors[] = ACP3_CMS::$lang->t('polls', 'type_in_answer');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => ACP3_CMS::$date->toSQL($_POST['start']),
			'end' => ACP3_CMS::$date->toSQL($_POST['end']),
			'question' => str_encode($_POST['question']),
			'multiple' => isset($_POST['multiple']) ? '1' : '0',
			'user_id' => ACP3_CMS::$auth->getUserId(),
		);

		$bool = ACP3_CMS::$db2->insert(DB_PRE . 'polls', $insert_values);
		$poll_id = ACP3_CMS::$db2->lastInsertId();
		$bool2 = false;

		if ($bool !== false) {
			foreach ($_POST['answers'] as $row) {
				if (!empty($row)) {
					$insert_answer = array(
						'id' => '',
						'text' => str_encode($row),
						'poll_id' => $poll_id,
					);
					$bool2 = ACP3_CMS::$db2->insert(DB_PRE . 'poll_answers', $insert_answer);
				}
			}
		}

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool && $bool2, ACP3_CMS::$lang->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/polls');
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
	ACP3_CMS::$view->assign('publication_period', ACP3_CMS::$date->datepicker(array('start', 'end')));
	ACP3_CMS::$view->assign('question', isset($_POST['question']) ? $_POST['question'] : '');
	ACP3_CMS::$view->assign('answers', $answers);
	ACP3_CMS::$view->assign('multiple', selectEntry('multiple', '1', '0', 'checked'));
	ACP3_CMS::$view->assign('disable', count($answers) < 10 ? false : true);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/acp_create.tpl'));
}
