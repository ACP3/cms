<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$breadcrumb->append($lang->t('system', 'maintenance'), $uri->route('acp/system/maintenance'))
		   ->append($lang->t('system', 'update_check'));

$file = @file_get_contents('http://www.acp3-cms.net/update.txt');
if ($file !== false) {
	$data = explode('||', $file);
	if (count($data) === 2) {
		$data[2] = CONFIG_VERSION;

		if (version_compare($data[2], $data[0], '>=')) {
			$tpl->assign('update_text', $lang->t('system', 'acp3_up_to_date'));
		} else {
			$tpl->assign('update_text', sprintf($lang->t('system', 'acp3_not_up_to_date'), '<a href="' . $data[1] . '" onclick="window.open(this.href); return false">', '</a>'));
		}
		$tpl->assign('update', $data);
	}
}
view::setContent(view::fetchTemplate('system/update_check.tpl'));