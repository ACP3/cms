<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

switch($_POST['area']) {
	case 'title':
		$fields = 'title, file';
		break;
	case 'content':
		$fields = 'text';
		break;
	default:
		$fields = 'title, file, text';
}

$period = '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
$result_files = ACP3_CMS::$db2->fetchAll('SELECT id, title, text FROM ' . DB_PRE . 'files WHERE MATCH (' . $fields . ') AGAINST (' . ACP3_CMS::$db2->quote($_POST['search_term']) . ' IN BOOLEAN MODE) AND ' . $period . ' ORDER BY start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', id ' . $_POST['sort'], array('time' => ACP3_CMS::$date->getCurrentDateTime()));
$c_result_files = count($result_files);

if ($c_result_files > 0) {
	$module_name = str_replace(MODULES_DIR, '', __DIR__);
	$name =  ACP3_CMS::$lang->t($module_name, $module_name);
	$results_mods[$name]['dir'] = $module_name;
	for ($i = 0; $i < $c_result_files; ++$i) {
		$results_mods[$name]['results'][$i]['hyperlink'] = ACP3_CMS::$uri->route('files/details/id_' . $result_files[$i]['id']);
		$results_mods[$name]['results'][$i]['headline'] = $result_files[$i]['title'];
		$results_mods[$name]['results'][$i]['text'] = shortenEntry($result_files[$i]['text'], 200, 0, '...');
	}
}
