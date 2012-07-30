<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$auth->logout();

if ($uri->last) {
	$lastPage = base64_decode($uri->last);
	if (!preg_match('/^((acp|users)\/)/', $lastPage))
		$uri->redirect($lastPage);
}
$uri->redirect(0, ROOT_DIR);
