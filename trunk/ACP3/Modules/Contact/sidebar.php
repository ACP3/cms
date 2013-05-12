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

$settings = ACP3\Core\Config::getSettings('contact');
$settings['address'] = ACP3\Core\Functions::rewriteInternalUri($settings['address']);
$settings['disclaimer'] = ACP3\Core\Functions::rewriteInternalUri($settings['disclaimer']);
ACP3\CMS::$injector['View']->assign('sidebar_contact', $settings);

ACP3\CMS::$injector['View']->displayTemplate('contact/sidebar.tpl');