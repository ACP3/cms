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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true) {
	require_once MODULES_DIR . 'gallery/functions.php';

	if ((ACP3_CMS::$uri->action === 'up' || ACP3_CMS::$uri->action === 'down') && ACP3_CMS::$db->countRows('*', 'gallery_pictures', 'id = \'' . ACP3_CMS::$uri->id . '\'') == 1) {
		moveOneStep(ACP3_CMS::$uri->action, 'gallery_pictures', 'id', 'pic', ACP3_CMS::$uri->id, 'gallery');

		$gallery = ACP3_CMS::$db->select('g.id', 'gallery AS g, {pre}gallery_pictures AS p', 'p.id = \'' . ACP3_CMS::$uri->id . '\' AND p.gallery_id = g.id');
		setGalleryCache($gallery[0]['id']);

		ACP3_CMS::$uri->redirect('acp/gallery/edit/id_' . $gallery[0]['id']);
	}
}
ACP3_CMS::$uri->redirect('errors/404');
