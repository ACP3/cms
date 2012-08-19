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

$settings = ACP3_Config::getModuleSettings('contact');
$settings['address'] = $db->escape($settings['address'], 3);
$settings['telephone'] = $db->escape($settings['telephone'], 3);
$settings['fax'] = $db->escape($settings['fax'], 3);
$settings['disclaimer'] = $db->escape($settings['disclaimer'], 3);
$tpl->assign('imprint', $settings);

$tpl->assign('powered_by', sprintf($lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));

ACP3_View::setContent(ACP3_View::fetchTemplate('contact/imprint.tpl'));