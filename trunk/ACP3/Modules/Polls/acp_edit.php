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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	if (isset($_POST['submit']) === true) {
		if (ACP3\Core\Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = ACP3\CMS::$injector['Lang']->t('system', 'select_date');
		if (empty($_POST['title']))
			$errors['title'] = ACP3\CMS::$injector['Lang']->t('polls', 'type_in_question');
		$j = 0;
		foreach ($_POST['answers'] as $row) {
			if (!empty($row['value']))
				$check_answers = true;
			if (isset($row['delete']))
				++$j;
		}
		if (!isset($check_answers))
			$errors[] = ACP3\CMS::$injector['Lang']->t('polls', 'type_in_answer');
		if (count($_POST['answers']) - $j < 2)
			$errors[] = ACP3\CMS::$injector['Lang']->t('polls', 'can_not_delete_all_answers');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			// Frage aktualisieren
			$update_values = array(
				'start' => ACP3\CMS::$injector['Date']->toSQL($_POST['start']),
				'end' => ACP3\CMS::$injector['Date']->toSQL($_POST['end']),
				'title' => ACP3\Core\Functions::str_encode($_POST['title']),
				'multiple' => isset($_POST['multiple']) ? '1' : '0',
				'user_id' => ACP3\CMS::$injector['Auth']->getUserId(),
			);

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'polls', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));

			// Stimmen zurücksetzen
			if (!empty($_POST['reset']))
				ACP3\CMS::$injector['Db']->delete(DB_PRE . 'poll_votes', array('poll_id' => ACP3\CMS::$injector['URI']->id));

			// Antworten
			foreach ($_POST['answers'] as $row) {
				// Neue Antwort hinzufügen
				if (empty($row['id'])) {
					// Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
					if (!empty($row['value']) && !isset($row['delete']))
						ACP3\CMS::$injector['Db']->insert(DB_PRE . 'poll_answers', array('text' => ACP3\Core\Functions::str_encode($row['value']), 'poll_id' => ACP3\CMS::$injector['URI']->id));
				// Antwort mitsamt Stimmen löschen
				} elseif (isset($row['delete']) && ACP3\Core\Validate::isNumber($row['id'])) {
					ACP3\CMS::$injector['Db']->delete(DB_PRE . 'poll_answers', array('id' => $row['id']));
					if (!empty($_POST['reset']))
						ACP3\CMS::$injector['Db']->delete(DB_PRE . 'poll_votes', array('answer_id' => $row['id']));
				// Antwort aktualisieren
				} elseif (!empty($row['value']) && ACP3\Core\Validate::isNumber($row['id'])) {
					$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'poll_answers', array('text' => ACP3\Core\Functions::str_encode($row['value'])), array('id' => $row['id']));
				}
			}

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/polls');
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
			$answers = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, text FROM ' . DB_PRE . 'poll_answers WHERE poll_id = ?', array(ACP3\CMS::$injector['URI']->id));
			$c_answers = count($answers);

			for ($i = 0; $i < $c_answers; ++$i) {
				$answers[$i]['number'] = $i;
				$answers[$i]['id'] = $answers[$i]['id'];
				$answers[$i]['value'] = $answers[$i]['text'];
			}
		}
		ACP3\CMS::$injector['View']->assign('answers', $answers);

		$poll = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT start, end, title, multiple FROM ' . DB_PRE . 'polls WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

		$options = array();
		$options[0]['name'] = 'reset';
		$options[0]['checked'] = ACP3\Core\Functions::selectEntry('reset', '1', '0', 'checked');
		$options[0]['lang'] = ACP3\CMS::$injector['Lang']->t('polls', 'reset_votes');
		$options[1]['name'] = 'multiple';
		$options[1]['checked'] = ACP3\Core\Functions::selectEntry('multiple', '1', $poll['multiple'], 'checked');
		$options[1]['lang'] = ACP3\CMS::$injector['Lang']->t('polls', 'multiple_choice');
		ACP3\CMS::$injector['View']->assign('options', $options);

		// Übergabe der Daten an Smarty
		ACP3\CMS::$injector['View']->assign('publication_period', ACP3\CMS::$injector['Date']->datepicker(array('start', 'end'), array($poll['start'], $poll['end'])));
		ACP3\CMS::$injector['View']->assign('title', isset($_POST['title']) ? $_POST['title'] : $poll['title']);
		ACP3\CMS::$injector['View']->assign('disable', count($answers) < 10 ? false : true);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
