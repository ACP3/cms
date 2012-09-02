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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'polls', 'id = \'' . ACP3_CMS::$uri->id . '\'') == 1) {
	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = ACP3_CMS::$lang->t('common', 'select_date');
		if (empty($_POST['question']))
			$errors['question'] = ACP3_CMS::$lang->t('polls', 'type_in_question');
		$j = 0;
		foreach ($_POST['answers'] as $row) {
			if (!empty($row['value']))
				$check_answers = true;
			if (isset($row['delete']))
				++$j;
		}
		if (!isset($check_answers))
			$errors[] = ACP3_CMS::$lang->t('polls', 'type_in_answer');
		if (count($_POST['answers']) - $j < 2)
			$errors[] = ACP3_CMS::$lang->t('polls', 'can_not_delete_all_answers');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
		} else {
			// Frage aktualisieren
			$update_values = array(
				'start' => $_POST['start'],
				'end' => $_POST['end'],
				'question' => ACP3_CMS::$db->escape($_POST['question']),
				'multiple' => isset($_POST['multiple']) ? '1' : '0',
				'user_id' => ACP3_CMS::$auth->getUserId(),
			);

			$bool = ACP3_CMS::$db->update('polls', $update_values, 'id = \'' . ACP3_CMS::$uri->id . '\'');

			// Stimmen zurücksetzen
			if (!empty($_POST['reset']))
				ACP3_CMS::$db->delete('poll_votes', 'poll_id = \'' . ACP3_CMS::$uri->id . '\'');

			// Antworten
			foreach ($_POST['answers'] as $row) {
				// Neue Antwort hinzufügen
				if (empty($row['id'])) {
					// Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
					if (!empty($row['value']) && !isset($row['delete']))
						ACP3_CMS::$db->insert('poll_answers', array('text' => ACP3_CMS::$db->escape($row['value']), 'poll_id' => ACP3_CMS::$uri->id));
				// Antwort mitsamt Stimmen löschen
				} elseif (isset($row['delete']) && ACP3_Validate::isNumber($row['id'])) {
					ACP3_CMS::$db->delete('poll_answers', 'id = \'' . $row['id'] . '\'');
					if (!empty($_POST['reset']))
						ACP3_CMS::$db->delete('poll_votes', 'answer_id = \'' . $row['id'] . '\'');
				// Antwort aktualisieren
				} elseif (!empty($row['value']) && ACP3_Validate::isNumber($row['id'])) {
					$bool = ACP3_CMS::$db->update('poll_answers', array('text' => ACP3_CMS::$db->escape($row['value'])), 'id = \'' . $row['id'] . '\'');
				}
			}

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/polls');
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
			$answers = ACP3_CMS::$db->select('id, text', 'poll_answers', 'poll_id = \'' . ACP3_CMS::$uri->id . '\'');
			$c_answers = count($answers);

			for ($i = 0; $i < $c_answers; ++$i) {
				$answers[$i]['number'] = $i;
				$answers[$i]['id'] = $answers[$i]['id'];
				$answers[$i]['value'] = $answers[$i]['text'];
			}
		}
		ACP3_CMS::$view->assign('answers', $answers);

		$poll = ACP3_CMS::$db->select('start, end, question, multiple', 'polls', 'id = \'' . ACP3_CMS::$uri->id . '\'');

		$options = array();
		$options[0]['name'] = 'reset';
		$options[0]['checked'] = selectEntry('reset', '1', '0', 'checked');
		$options[0]['lang'] = ACP3_CMS::$lang->t('polls', 'reset_votes');
		$options[1]['name'] = 'multiple';
		$options[1]['checked'] = selectEntry('multiple', '1', $poll[0]['multiple'], 'checked');
		$options[1]['lang'] = ACP3_CMS::$lang->t('polls', 'multiple_choice');
		ACP3_CMS::$view->assign('options', $options);

		// Übergabe der Daten an Smarty
		ACP3_CMS::$view->assign('publication_period', ACP3_CMS::$date->datepicker(array('start', 'end'), array($poll[0]['start'], $poll[0]['end'])));
		ACP3_CMS::$view->assign('question', isset($_POST['question']) ? $_POST['question'] : ACP3_CMS::$db->escape($poll[0]['question'], 3));
		ACP3_CMS::$view->assign('disable', count($answers) < 10 ? false : true);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
