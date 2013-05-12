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
ACP3\CMS::$injector['View']->assign('imprint', $settings);

ACP3\CMS::$injector['View']->assign('powered_by', sprintf(ACP3\CMS::$injector['Lang']->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));