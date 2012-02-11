<?php
/**
 * Contact
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$settings = config::getModuleSettings('contact');
$settings['address'] = $db->escape($settings['address'], 3);
$settings['telephone'] = $db->escape($settings['telephone'], 3);
$settings['fax'] = $db->escape($settings['fax'], 3);
$settings['mail'] = '<a href="' . $uri->route('contact') . '">' . $settings['mail'] . '</a>';
$settings['disclaimer'] = $db->escape($settings['disclaimer'], 3);
$settings['layout'] = $db->escape($settings['layout'], 3);

// Platzhalteer ersetzen
$search = array('{address_lang}', '{address_value}', '{email_lang}', '{email_value}', '{telephone_lang}', '{telephone_value}', '{fax_lang}', '{fax_value}', '{disclaimer_lang}', '{disclaimer_value}');
$replace = array($lang->t('contact', 'address'), $settings['address'], $lang->t('common', 'email'), $settings['mail'], $lang->t('contact', 'telephone'), $settings['telephone'], $lang->t('contact', 'fax'), $settings['fax'], $lang->t('contact', 'disclaimer'), $settings['disclaimer']);

$tpl->assign('imprint', rewriteInternalUri(str_replace($search, $replace, $settings['layout'])));
$tpl->assign('powered_by', sprintf($lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));

view::setContent(view::fetchTemplate('contact/imprint.tpl'));