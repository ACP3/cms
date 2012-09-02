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

$bool = ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true ? ACP3_CMS::$db->update('newsletter_accounts', array('hash' => ''), 'id = \'' . ACP3_CMS::$uri->id . '\'') : false;

setRedirectMessage($bool, $bool !== false ? ACP3_CMS::$lang->t('newsletter', 'activate_success') : ACP3_CMS::$lang->t('newsletter', 'activate_error'), 'acp/newsletter');