<?php
if (!defined('IN_ACP'))
	exit;

$breadcrumb->assign(lang('system', 'system'), uri('acp/system'));
$breadcrumb->assign(lang('system', 'extensions'));

$content = $tpl->fetch('system/acp_extensions.html');
?>