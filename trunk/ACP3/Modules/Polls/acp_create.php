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
	if (ACP3\Core\Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = ACP3\CMS::$injector['Lang']->t('system', 'select_date');
	if (empty($_POST['title']))
		$errors['title'] = ACP3\CMS::$injector['Lang']->t('polls', 'type_in_question');
	$i = 0;
	foreach ($_POST['answers'] as $row) {
		if (!empty($row))
			++$i;
	}
	if ($i <= 1)
		$errors[] = ACP3\CMS::$injector['Lang']->t('polls', 'type_in_answer');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => ACP3\CMS::$injector['Date']->toSQL($_POST['start']),
			'end' => ACP3\CMS::$injector['Date']->toSQL($_POST['end']),
			'title' => ACP3\Core\Functions::str_encode($_POST['title']),
			'multiple' => isset($_POST['multiple']) ? '1' : '0',
			'user_id' => ACP3\CMS::$injector['Auth']->getUserId(),
		);

		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'polls', $insert_values);
		$poll_id = ACP3\CMS::$injector['Db']->lastInsertId();
		$bool2 = false;

		if ($bool !== false) {
			foreach ($_POST['answers'] as $row) {
				if (!empty($row)) {
					$insert_answer = array(
						'id' => '',
						'text' => ACP3\Core\Functions::str_encode($row),
						'poll_id' => $poll_id,
					);
					$bool2 = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'poll_answers', $insert_answer);
				}
			}
		}

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool && $bool2, ACP3\CMS::$injector['Lang']->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/polls');
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
	ACP3\CMS::$injector['View']->assign('publication_period', ACP3\CMS::$injector['Date']->datepicker(array('start', 'end')));
	ACP3\CMS::$injector['View']->assign('title', isset($_POST['title']) ? $_POST['title'] : '');
	ACP3\CMS::$injector['View']->assign('answers', $answers);
	ACP3\CMS::$injector['View']->assign('multiple', ACP3\Core\Functions::selectEntry('multiple', '1', '0', 'checked'));
	ACP3\CMS::$injector['View']->assign('disable', count($answers) < 10 ? false : true);

	ACP3\CMS::$injector['Session']->generateFormToken();
}
