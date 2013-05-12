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

if (ACP3\Core\Modules::check('captcha', 'functions') === true) {
	require_once MODULES_DIR . 'captcha/functions.php';
	ACP3\CMS::$injector['View']->assign('captcha', captcha());
}

ACP3\CMS::$injector['Session']->generateFormToken('newsletter/list');

ACP3\CMS::$injector['View']->displayTemplate('newsletter/sidebar.tpl');