<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$files = ACP3_CMS::$db->select('id, start, end, file, size, link_title', 'files', 0, 'start DESC, end DESC, id DESC', POS, ACP3_CMS::$auth->entries);
$c_files = count($files);

if ($c_files > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'files')));
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['period'] = ACP3_CMS::$date->period($files[$i]['start'], $files[$i]['end']);
		$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : ACP3_CMS::$lang->t('files', 'unknown_filesize');
		$files[$i]['link_title'] = ACP3_CMS::$db->escape($files[$i]['link_title'], 3);
	}
	ACP3_CMS::$view->assign('files', $files);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('files', 'acp_delete'));
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('files/acp_list.tpl'));
