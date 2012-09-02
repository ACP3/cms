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
$settings['address'] = ACP3_CMS::$db->escape($settings['address'], 3);
$settings['telephone'] = ACP3_CMS::$db->escape($settings['telephone'], 3);
$settings['fax'] = ACP3_CMS::$db->escape($settings['fax'], 3);
$settings['disclaimer'] = rewriteInternalUri(ACP3_CMS::$db->escape($settings['disclaimer'], 3));
ACP3_CMS::$view->assign('imprint', $settings);

ACP3_CMS::$view->assign('powered_by', sprintf(ACP3_CMS::$lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('contact/imprint.tpl'));