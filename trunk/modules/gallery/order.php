<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (validate::isNumber($uri->id) === true) {
	require_once MODULES_DIR . 'gallery/functions.php';

	if (($uri->action === 'up' || $uri->action === 'down') && $db->countRows('*', 'gallery_pictures', 'id = \'' . $uri->id . '\'') == 1) {
		$gallery = $db->select('g.name, g.id', 'gallery AS g, {pre}gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');

		breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
		breadcrumb::assign($lang->t('gallery', 'gallery'), $uri->route('acp/gallery'));
		breadcrumb::assign($gallery[0]['name'], $uri->route('acp/gallery/edit_gallery/id_' . $gallery[0]['id']));
		breadcrumb::assign($lang->t('common', 'edit_order'));

		moveOneStep($uri->action, 'gallery_pictures', 'id', 'pic', $uri->id);
		setGalleryCache($gallery[0]['id']);

		$uri->redirect('acp/gallery/edit_gallery/id_' . $gallery[0]['id']);
	}
}
$uri->redirect('acp/errors/404');
