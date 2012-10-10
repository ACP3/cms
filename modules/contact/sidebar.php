<?php
/**
 * Contact
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$settings = ACP3_Config::getSettings('contact');
$settings['address'] = rewriteInternalUri($settings['address']);
$settings['disclaimer'] = rewriteInternalUri($settings['disclaimer']);
ACP3_CMS::$view->assign('sidebar_contact', $settings);

ACP3_CMS::$view->displayTemplate('contact/sidebar.tpl');