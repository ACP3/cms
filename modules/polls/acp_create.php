<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/polls/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', publication_period('start'));
	$tpl->assign('end_date', publication_period('end'));

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

	$content = $tpl->fetch('polls/acp_create.html');
}
?>