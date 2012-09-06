<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$bool = false;
if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true) {
	$bool = ACP3_CMS::$db2->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('id' => ACP3_CMS::$uri->id));
}

setRedirectMessage($bool, ACP3_CMS::$lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), 'acp/newsletter');