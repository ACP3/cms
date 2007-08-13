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
if (!$modules->check())
	redirect('errors/403');
if (isset($_POST['submit'])) {
	include 'modules/polls/entry.php';
}
if (!isset($_POST['submit']) || isset($error_msg)) {
	$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

	// Datumsauswahl
	$tpl->assign('start_day', date_dropdown('day', 'start_day', 'start_day'));
	$tpl->assign('start_month', date_dropdown('month', 'start_month', 'start_month'));
	$tpl->assign('start_year', date_dropdown('year', 'start_year', 'start_year'));
	$tpl->assign('start_hour', date_dropdown('hour', 'start_hour', 'start_hour'));
	$tpl->assign('start_min', date_dropdown('min', 'start_min', 'start_min'));
	$tpl->assign('end_day', date_dropdown('day', 'end_day', 'end_day'));
	$tpl->assign('end_month', date_dropdown('month', 'end_month', 'end_month'));
	$tpl->assign('end_year', date_dropdown('year', 'end_year', 'end_year'));
	$tpl->assign('end_hour', date_dropdown('hour', 'end_hour', 'end_hour'));
	$tpl->assign('end_min', date_dropdown('min', 'end_min', 'end_min'));

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