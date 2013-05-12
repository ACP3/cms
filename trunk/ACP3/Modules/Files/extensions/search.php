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
$result_files = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, title, text FROM ' . DB_PRE . 'files WHERE MATCH (' . $fields . ') AGAINST (' . ACP3\CMS::$injector['Db']->quote($_POST['search_term']) . ' IN BOOLEAN MODE) AND ' . $period . ' ORDER BY start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', id ' . $_POST['sort'], array('time' => ACP3\CMS::$injector['Date']->getCurrentDateTime()));
$c_result_files = count($result_files);

if ($c_result_files > 0) {
	$name =  ACP3\CMS::$injector['Lang']->t('files', 'files');
	$results_mods[$name]['dir'] = 'files';
	for ($i = 0; $i < $c_result_files; ++$i) {
		$results_mods[$name]['results'][$i]['hyperlink'] = ACP3\CMS::$injector['URI']->route('files/details/id_' . $result_files[$i]['id']);
		$results_mods[$name]['results'][$i]['title'] = $result_files[$i]['title'];
		$results_mods[$name]['results'][$i]['text'] = ACP3\Core\Functions::shortenEntry($result_files[$i]['text'], 200, 0, '...');
	}
}
