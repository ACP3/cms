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

$bool = validate::isNumber($uri->id) ? $db->update('newsletter_accounts', array('hash' => ''), 'id = \'' . $uri->id . '\'') : false;

$content = comboBox($bool !== null ? $lang->t('newsletter', 'activate_success') : $lang->t('newsletter', 'activate_error'), $uri->route('acp/newsletter'));
