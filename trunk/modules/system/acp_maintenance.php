<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

$breadcrumb->assign(lang('system', 'system'), uri('acp/system'));
$breadcrumb->assign(lang('system', 'maintenance'));

$content = $tpl->fetch('system/acp_maintenance.html');
?>