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

if (validate::isNumber($uri->id) && $db->select('id', 'gallery', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	$gallery = $db->select('start, end, name', 'gallery', 'id = \'' . $uri->id . '\'');

	breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
	breadcrumb::assign($lang->t('gallery', 'gallery'), uri('acp/gallery'));
	breadcrumb::assign($gallery[0]['name']);

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($form['name']) < 3)
			$errors[] = $lang->t('gallery', 'type_in_gallery_name');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$start_date = $date->timestamp($form['start']);
			$end_date = $date->timestamp($form['end']);

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'name' => $db->escape($form['name']),
			);

			$bool = $db->update('gallery', $update_values, 'id = \'' . $uri->id . '\'');

			$content = comboBox($bool ? $lang->t('gallery', 'edit_success') : $lang->t('gallery', 'edit_error'), uri('acp/gallery'));
		}
	}
	if (!isset($_POST['entries']) && !isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$tpl->assign('gallery_id', $uri->id);

		// Datumsauswahl
		$tpl->assign('start_date', datepicker('start', $gallery[0]['start']));
		$tpl->assign('end_date', datepicker('end', $gallery[0]['end']));

		$tpl->assign('form', isset($form) ? $form : $gallery[0]);

		$pictures = $db->select('id, pic, file, description', 'gallery_pictures', 'gallery_id = \'' . $uri->id . '\'', 'pic ASC', POS, CONFIG_ENTRIES);
		$c_pictures = count($pictures);

		if ($c_pictures > 0) {
			$tpl->assign('pagination', pagination($db->select('id', 'gallery_pictures', 'gallery_id = \'' . $uri->id . '\'', 0, 0, 0, 1)));
			for ($i = 0; $i < $c_pictures; ++$i) {
				$pictures[$i]['description'] = $db->escape($pictures[$i]['description'], 3);
			}
			$tpl->assign('pictures', $pictures);
		}

		$content = $tpl->fetch('gallery/edit_gallery.html');
	}
} else {
	redirect('errors/404');
}
?>