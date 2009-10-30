<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
breadcrumb::assign($lang->t('system', 'system'), uri('acp/system'));
breadcrumb::assign($lang->t('system', 'maintenance'), uri('acp/system/maintenance'));
breadcrumb::assign($lang->t('system', 'update_check'));

$file = @file_get_contents('http://www.acp3-cms.net/update.txt');
if ($file) {
	$content = explode('||', $file);
	if (count($content) == 2) {
		$content[2] = CONFIG_VERSION;

		if (version_compare($content[2], $content[0], '>=')) {
			$tpl->assign('update_text', $lang->t('system', 'acp3_up_to_date'));
		} else {
			$tpl->assign('update_text', sprintf($lang->t('system', 'acp3_not_up_to_date'), '<a href="' . $content[1] . '" onclick="window.open(this.href); return false">', '</a>'));
		}
		$tpl->assign('update', $content);
	}
}
$content = $tpl->fetch('system/update_check.html');
