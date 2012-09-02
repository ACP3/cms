<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$galleries = ACP3_CMS::$db->select('id, start, end, name', 'gallery', 0, 'start DESC, end DESC, id DESC', POS, ACP3_CMS::$auth->entries);
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'gallery')));
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['period'] = ACP3_CMS::$date->period($galleries[$i]['start'], $galleries[$i]['end']);
		$galleries[$i]['name'] = ACP3_CMS::$db->escape($galleries[$i]['name'], 3);
		$galleries[$i]['pictures'] = ACP3_CMS::$db->countRows('*', 'gallery_pictures', 'gallery_id = \'' . $galleries[$i]['id'] . '\'');
	}
	ACP3_CMS::$view->assign('galleries', $galleries);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('gallery', 'acp_delete'));
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('gallery/acp_list.tpl'));
