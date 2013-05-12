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

ACP3\Core\Functions::getRedirectMessage();

$files = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, end, file, size, title FROM ' . DB_PRE . 'files ORDER BY start DESC, end DESC, id DESC');
$c_files = count($files);

if ($c_files > 0) {
	$can_delete = ACP3\Core\Modules::check('files', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 1 : 0,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['period'] = ACP3\CMS::$injector['Date']->formatTimeRange($files[$i]['start'], $files[$i]['end']);
		$files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : ACP3\CMS::$injector['Lang']->t('files', 'unknown_filesize');
	}
	ACP3\CMS::$injector['View']->assign('files', $files);
	ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
}