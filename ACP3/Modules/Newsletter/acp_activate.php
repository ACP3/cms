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
if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true) {
	$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('id' => ACP3\CMS::$injector['URI']->id));
}

ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), 'acp/newsletter');