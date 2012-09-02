<?php
/**
 * Polls
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'polls', 'id = \'' . ACP3_CMS::$uri->id . '\' AND start <= \'' . ACP3_CMS::$date->getCurrentDateTime() . '\'') == 1) {
	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('polls', 'polls'), ACP3_CMS::$uri->route('polls'))
			   ->append(ACP3_CMS::$lang->t('polls', 'result'));

	$question = ACP3_CMS::$db->select('question', 'polls');
	$answers = ACP3_CMS::$db->select('id, text', 'poll_answers', 'poll_id = \'' . ACP3_CMS::$uri->id . '\'', 'id ASC');
	$c_answers = count($answers);
	$total_votes = ACP3_CMS::$db->countRows('answer_id', 'poll_votes', 'poll_id = \'' . ACP3_CMS::$uri->id . '\'');

	for ($i = 0; $i < $c_answers; ++$i) {
		$answers[$i]['text'] = ACP3_CMS::$db->escape($answers[$i]['text'], 3);
		$answers[$i]['votes'] = ACP3_CMS::$db->countRows('answer_id', 'poll_votes', 'answer_id = \'' . $answers[$i]['id'] . '\'');
		$answers[$i]['percent'] = $total_votes > '0' ? round(100 * $answers[$i]['votes'] / $total_votes, 2) : '0';
	}
	ACP3_CMS::$view->assign('question', ACP3_CMS::$db->escape($question[0]['question'], 3));
	ACP3_CMS::$view->assign('answers', $answers);
	ACP3_CMS::$view->assign('total_votes', $total_votes);

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/result.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
