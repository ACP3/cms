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

$content = combo_box($bool ? lang('newsletter', 'nl_activate_success') : lang('newsletter', 'nl_activate_error'), uri('acp/newsletter'));
?>