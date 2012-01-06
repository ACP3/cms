<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$auth->logout();

if ($uri->last) {
	$lastPage = base64_decode($uri->last);
	if (!preg_match('/^(acp\/)/', $lastPage) && !preg_match('/^(users\/)/', $lastPage)) {
		$uri->redirect($lastPage);
	}
}
$uri->redirect(0, ROOT_DIR);
