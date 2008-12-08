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
	if (($uri->mode == 'up' || $uri->mode == 'down') && $db->select('id', 'gallery_pictures', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == 1) {
		$gallery = $db->select('g.name, g.id', 'gallery AS g, ' . CONFIG_DB_PRE . 'gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');

		breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
		breadcrumb::assign($lang->t('gallery', 'gallery'), uri('acp/gallery'));
		breadcrumb::assign($gallery[0]['name'], uri('acp/gallery/edit_gallery/id_' . $gallery[0]['id']));
		breadcrumb::assign($lang->t('common', 'edit_order'));

		$bool = moveOneStep($uri->mode, 'gallery_pictures', 'id', 'pic', $uri->id);

		$content = comboBox($bool ? $lang->t('common', 'order_success') : $lang->t('common', 'order_error'), uri('acp/gallery/edit_gallery/id_' . $gallery[0]['id']));
	} else {
		redirect('errors/404');
	}
} else {
	redirect('errors/404');
}
?>