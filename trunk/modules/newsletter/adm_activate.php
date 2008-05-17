<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$bool = validate::isNumber($uri->id) ? $db->update('newsletter_accounts', array('hash' => ''), 'id = \'' . $uri->id . '\'') : false;

$content = comboBox($bool ? lang('newsletter', 'activate_success') : lang('newsletter', 'activate_error'), uri('acp/newsletter'));
?>