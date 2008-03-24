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

$bool = !empty($modules->id) ? $db->update('nl_accounts', array('hash', ''), 'id = \'' . $modules->id . '\'') : false;

$content = combo_box($bool ? lang('newsletter', 'activate_success') : lang('newsletter', 'activate_error'), uri('acp/newsletter'));
?>