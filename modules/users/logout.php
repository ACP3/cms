<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

$auth->logout();

if ($uri->last && !preg_match('/^(acp\/)/', base64_decode($uri->last))) {
	redirect(base64_decode($uri->last));
} else {
	redirect(0, ROOT_DIR);
}