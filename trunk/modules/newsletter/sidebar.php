<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
if (defined('IN_ACP3') === false)
	exit;

if (ACP3_Modules::check('captcha', 'functions') === true) {
	require_once MODULES_DIR . 'captcha/functions.php';
	ACP3_CMS::$view->assign('captcha', captcha());
}

ACP3_CMS::$session->generateFormToken('newsletter/list');

ACP3_CMS::$view->displayTemplate('newsletter/sidebar.tpl');