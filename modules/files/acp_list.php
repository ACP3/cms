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

$files = ACP3_CMS::$db2->fetchAll('SELECT id, start, end, file, size, link_title FROM ' . DB_PRE . 'files ORDER BY start DESC, end DESC, id DESC');
$c_files = count($files);

if ($c_files > 0) {
	$can_delete = ACP3_Modules::check('files', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::setContent(datatable($config));
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['period'] = ACP3_CMS::$date->period($files[$i]['start'], $files[$i]['end']);
		$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : ACP3_CMS::$lang->t('files', 'unknown_filesize');
	}
	ACP3_CMS::$view->assign('files', $files);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
}

ACP3_CMS::appendContent(ACP3_CMS::$view->fetchTemplate('files/acp_list.tpl'));
