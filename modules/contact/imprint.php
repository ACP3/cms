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
$contact['disclaimer'] = $db->escape($contact['disclaimer'], 3);
$contact['miscellaneous'] = $db->escape($contact['miscellaneous'], 3);

$tpl->assign('imprint', $contact);
$tpl->assign('powered_by', sprintf($lang->t('contact', 'powered_by'), '<a href="http://www.acp3-cms.net" onclick="window.open(this.href); return false">ACP3</a>'));

$content = $tpl->fetch('contact/imprint.html');
?>