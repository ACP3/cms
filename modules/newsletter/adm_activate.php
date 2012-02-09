<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$bool = validate::isNumber($uri->id) === true ? $db->update('newsletter_accounts', array('hash' => ''), 'id = \'' . $uri->id . '\'') : false;

setRedirectMessage($bool !== false ? $lang->t('newsletter', 'activate_success') : $lang->t('newsletter', 'activate_error'), 'acp/newsletter');