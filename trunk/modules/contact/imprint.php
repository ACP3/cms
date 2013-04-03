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
ACP3_CMS::$view->assign('imprint', $settings);

ACP3_CMS::$view->assign('powered_by', sprintf(ACP3_CMS::$lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));