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

if (validate::isNumber($modules->id) && $db->select('id', 'gallery', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$gallery = $db->select('start, end, name', 'gallery', 'id = \'' . $modules->id . '\'');

	breadcrumb::assign(lang('common', 'acp'), uri('acp'));
	breadcrumb::assign(lang('gallery', 'gallery'), uri('acp/gallery'));
	breadcrumb::assign($gallery[0]['name']);

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start']) || !validate::date($form['end']))
			$errors[] = lang('common', 'select_date');
		if (strlen($form['name']) < 3)
			$errors[] = lang('gallery', 'type_in_gallery_name');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$start_date = strtotime($form['start'], dateAligned(2, time()));
			$end_date = strtotime($form['end'], dateAligned(2, time()));

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'name' => $db->escape($form['name']),
			);

			$bool = $db->update('gallery', $update_values, 'id = \'' . $modules->id . '\'');

			cache::create('gallery_pics_id_' . $modules->id, $db->query('SELECT g.name, p.id FROM ' . CONFIG_DB_PRE . 'gallery g LEFT JOIN ' . CONFIG_DB_PRE . 'galpics p ON g.id = \'' . $modules->id . '\' AND p.gallery_id = \'' . $modules->id . '\' ORDER BY p.pic ASC, p.id ASC'));

			$content = comboBox($bool ? lang('gallery', 'edit_success') : lang('gallery', 'edit_error'), uri('acp/gallery'));
		}
	}
	if (!isset($_POST['entries']) && !isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$tpl->assign('gallery_id', $modules->id);

		// Datumsauswahl
		$tpl->assign('start_date', datepicker('start', $gallery[0]['start']));
		$tpl->assign('end_date', datepicker('end', $gallery[0]['end']));

		$tpl->assign('form', isset($form) ? $form : $gallery[0]);

		$pictures = $db->select('id, pic, file, description', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 'pic ASC', POS, CONFIG_ENTRIES);
		$c_pictures = count($pictures);

		if ($c_pictures > 0) {
			$tpl->assign('pagination', $modules->pagination($db->select('id', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 0, 0, 0, 1)));
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