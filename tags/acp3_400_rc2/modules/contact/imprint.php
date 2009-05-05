<?php
/**
 * Contact
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

breadcrumb::assign($lang->t('contact', 'contact'), uri('contact'));
breadcrumb::assign($lang->t('contact', 'imprint'));

$contact = config::output('contact');
$contact['address'] = $db->escape($contact['address'], 3);
$contact['mail'] = '<a href="' . uri('contact') . '">' . $contact['mail'] . '</a>';
$contact['disclaimer'] = $db->escape($contact['disclaimer'], 3);
$contact['layout'] = $db->escape($contact['layout'], 3);

// Platzhalteer ersetzen
$search = array('{address_lang}', '{address_value}', '{email_lang}', '{email_value}', '{telephone_lang}', '{telephone_value}', '{fax_lang}', '{fax_value}', '{disclaimer_lang}', '{disclaimer_value}');
$replace = array($lang->t('contact', 'address'), $contact['address'], $lang->t('common', 'email'), $contact['mail'], $lang->t('contact', 'telephone'), $contact['telephone'], $lang->t('contact', 'fax'), $contact['fax'], $lang->t('contact', 'disclaimer'), $contact['disclaimer']);

$tpl->assign('imprint', str_replace($search, $replace, $contact['layout']));
$tpl->assign('powered_by', sprintf($lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));

$content = $tpl->fetch('contact/imprint.html');
?>