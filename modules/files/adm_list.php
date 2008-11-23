<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$files = $db->select('id, start, end, file, size, link_title', 'files', 0, 'start DESC, end DESC, id DESC', POS, CONFIG_ENTRIES);
$c_files = count($files);

if ($c_files > 0) {
	$tpl->assign('pagination', pagination($db->select('id', 'files', 0, 0, 0, 0, 1)));
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['period'] = $date->period($files[$i]['start'], $files[$i]['end']);
		$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $lang->t('files', 'unknown_filesize');
		$files[$i]['link_title'] = $files[$i]['link_title'];
	}
	$tpl->assign('files', $files);
}

$content = $tpl->fetch('files/adm_list.html');
?>