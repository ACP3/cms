<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

function setFilesCache($id)
{
	global $db;
	return cache::create('files_details_id_' . $id, $db->select('f.id, f.start, f.category_id, f.file, f.size, f.link_title, f.text, c.name AS category_name', 'files AS f, ' . CONFIG_DB_PRE . 'categories AS c', 'f.id = \'' . $id . '\' AND f.category_id = c.id'));
}
function getFilesCache($id)
{
	if (!cache::check('files_details_id_' . $id)) {
		setFilesCache($id);
	}
	return cache::output('files_details_id_' . $id);
}
?>
