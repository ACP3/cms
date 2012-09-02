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

ACP3_CMS::$auth->logout();

if (ACP3_CMS::$uri->last) {
	$lastPage = base64_decode(ACP3_CMS::$uri->last);
	if (!preg_match('/^((acp|users)\/)/', $lastPage))
		ACP3_CMS::$uri->redirect($lastPage);
}
ACP3_CMS::$uri->redirect(0, ROOT_DIR);
