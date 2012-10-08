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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = ? AND start <= ?', array(ACP3_CMS::$uri->id, ACP3_CMS::$date->getCurrentDateTime())) == 1) {
	getRedirectMessage();

	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('polls', 'polls'), ACP3_CMS::$uri->route('polls'))
	->append(ACP3_CMS::$lang->t('polls', 'result'));

	$question = ACP3_CMS::$db2->fetchAssoc('SELECT p.title, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.id = ?', array(ACP3_CMS::$uri->id));
	$answers = ACP3_CMS::$db2->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array(ACP3_CMS::$uri->id));
	$c_answers = count($answers);
	$total_votes = $question['total_votes'];

	for ($i = 0; $i < $c_answers; ++$i) {
		$answers[$i]['percent'] = $total_votes > '0' ? round(100 * $answers[$i]['votes'] / $total_votes, 2) : '0';
	}
	ACP3_CMS::$view->assign('question', $question['title']);
	ACP3_CMS::$view->assign('answers', $answers);
	ACP3_CMS::$view->assign('total_votes', $total_votes);

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/result.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
