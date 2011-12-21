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

if (validate::isNumber($uri->id) && $db->countRows('*', 'gallery', 'id = \'' . $uri->id . '\'') == '1') {
	$gallery = $db->select('start, end, name', 'gallery', 'id = \'' . $uri->id . '\'');
	$gallery[0]['alias'] = seo::getUriAlias('gallery/pics/id_' . $uri->id);
	$gallery[0]['seo_keywords'] = seo::getKeywordsOrDescription('gallery/pics/id_' . $uri->id);
	$gallery[0]['seo_description'] = seo::getKeywordsOrDescription('gallery/pics/id_' . $uri->id, 'description');

	breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
	breadcrumb::assign($lang->t('gallery', 'gallery'), $uri->route('acp/gallery'));
	breadcrumb::assign($gallery[0]['name']);

	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($form['name']) < 3)
			$errors[] = $lang->t('gallery', 'type_in_gallery_name');
		if (!validate::isUriSafe($form['alias']) || validate::UriAliasExists($form['alias'], 'gallery/pics/id_' . $uri->id))
			$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'name' => $db->escape($form['name']),
				'user_id' => $auth->getUserId(),
			);

			$bool = $db->update('gallery', $update_values, 'id = \'' . $uri->id . '\'');
			$bool2 = seo::insertUriAlias($form['alias'], 'gallery/pics/id_' . $uri->id, $db->escape($form['seo_keywords']), $db->escape($form['seo_description']));

			require_once MODULES_DIR . 'gallery/functions.php';
			$bool3 = generatePictureAliases($uri->id);

			$content = comboBox($bool && $bool2 && $bool3 ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('acp/gallery'));
		}
	}
	if (!isset($_POST['entries']) && !isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$tpl->assign('gallery_id', $uri->id);

		// Datumsauswahl
		$tpl->assign('publication_period', $date->datepicker(array('start', 'end'), array($gallery[0]['start'], $gallery[0]['end'])));

		$tpl->assign('form', isset($form) ? $form : $gallery[0]);

		$pictures = $db->select('id, pic, file, description', 'gallery_pictures', 'gallery_id = \'' . $uri->id . '\'', 'pic ASC', POS, $auth->entries);
		$c_pictures = count($pictures);

		if ($c_pictures > 0) {
			$tpl->assign('pagination', pagination($db->countRows('*', 'gallery_pictures', 'gallery_id = \'' . $uri->id . '\'')));
			for ($i = 0; $i < $c_pictures; ++$i) {
				$pictures[$i]['first'] = $i == 0 ? true : false;
				$pictures[$i]['last'] = $i == $c_pictures - 1 ? true : false;
			}
			$tpl->assign('pictures', $pictures);
		}

		$content = modules::fetchTemplate('gallery/edit_gallery.html');
	}
} else {
	$uri->redirect('errors/404');
}
