<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$files = $db->select('id, start, end, file, size, link_title', 'files', 0, 'start DESC, end DESC, id DESC', POS, $session->get('entries'));
$c_files = count($files);

if ($c_files > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'files')));
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['period'] = $date->period($files[$i]['start'], $files[$i]['end']);
		$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $lang->t('files', 'unknown_filesize');
		$files[$i]['link_title'] = $db->escape($files[$i]['link_title'], 3);
	}
	$tpl->assign('files', $files);
}

view::setContent(view::fetchTemplate('files/adm_list.tpl'));
