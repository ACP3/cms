<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (validate::isNumber($uri->id)) {
	require_once ACP3_ROOT . 'modules/gallery/functions.php';

	if (($uri->mode == 'up' || $uri->mode == 'down') && $db->countRows('*', 'gallery_pictures', 'id = \'' . $uri->id . '\'') == 1) {
		$gallery = $db->select('g.name, g.id', 'gallery AS g, ' . $db->prefix . 'gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');

		breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
		breadcrumb::assign($lang->t('gallery', 'gallery'), uri('acp/gallery'));
		breadcrumb::assign($gallery[0]['name'], uri('acp/gallery/edit_gallery/id_' . $gallery[0]['id']));
		breadcrumb::assign($lang->t('common', 'edit_order'));

		moveOneStep($uri->mode, 'gallery_pictures', 'id', 'pic', $uri->id);
		setGalleryCache($gallery[0]['id']);

		redirect('acp/gallery/edit_gallery/id_' . $gallery[0]['id']);
	}
}
redirect('acp/errors/404');
