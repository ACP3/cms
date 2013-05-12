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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = ? AND start <= ?', array(ACP3\CMS::$injector['URI']->id, ACP3\CMS::$injector['Date']->getCurrentDateTime())) == 1) {
	ACP3\Core\Functions::getRedirectMessage();

	ACP3\CMS::$injector['Breadcrumb']
	->append(ACP3\CMS::$injector['Lang']->t('polls', 'polls'), ACP3\CMS::$injector['URI']->route('polls'))
	->append(ACP3\CMS::$injector['Lang']->t('polls', 'result'));

	$question = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT p.title, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.id = ?', array(ACP3\CMS::$injector['URI']->id));
	$answers = ACP3\CMS::$injector['Db']->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array(ACP3\CMS::$injector['URI']->id));
	$c_answers = count($answers);
	$total_votes = $question['total_votes'];

	for ($i = 0; $i < $c_answers; ++$i) {
		$answers[$i]['percent'] = $total_votes > '0' ? round(100 * $answers[$i]['votes'] / $total_votes, 2) : '0';
	}
	ACP3\CMS::$injector['View']->assign('question', $question['title']);
	ACP3\CMS::$injector['View']->assign('answers', $answers);
	ACP3\CMS::$injector['View']->assign('total_votes', $total_votes);
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
