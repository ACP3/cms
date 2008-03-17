<?php
/**
 * Administration Control Panel
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$breadcrumb->assign(lang('common', 'acp'));

$content = $tpl->fetch('acp/adm_list.html');
?>