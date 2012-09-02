<?php
/**
 * System
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('system', 'acp_maintenance'), ACP3_CMS::$uri->route('acp/system/maintenance'))
		   ->append(ACP3_CMS::$lang->t('system', 'acp_update_check'));

$file = @file_get_contents('http://www.acp3-cms.net/update.txt');
if ($file !== false) {
	$data = explode('||', $file);
	if (count($data) === 2) {
		$update = array(
			'installed_version' => CONFIG_VERSION,
			'current_version' => $data[0],
		);

		if (version_compare($update['installed_version'], $update['current_version'], '>=')) {
			$update['text'] = ACP3_CMS::$lang->t('system', 'acp3_up_to_date');
			$update['class'] = 'success';
		} else {
			$update['text'] = sprintf(ACP3_CMS::$lang->t('system', 'acp3_not_up_to_date'), '<a href="' . $data[1] . '" onclick="window.open(this.href); return false">', '</a>');
			$update['class'] = 'error';
		}
		
		ACP3_CMS::$view->assign('update', $update);
	}
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('system/acp_update_check.tpl'));