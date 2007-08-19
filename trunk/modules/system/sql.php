<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/system/entry.php';
}
if (!isset($_POST['submit']) || isset($_POST['submit']) && (isset($error_msg) || (isset($results) && is_array($results)))) {
	$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

	if (isset($results) && is_array($results)) {
		$tpl->assign('results', $results);
	}
	$content = $tpl->fetch('system/sql.html');
}
?>