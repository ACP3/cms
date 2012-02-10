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

if (validate::isNumber($uri->id) === true && $db->countRows('*', 'gallery', 'id = \'' . $uri->id . '\'') == '1') {
	$gallery = $db->select('start, end, name', 'gallery', 'id = \'' . $uri->id . '\'');
	$gallery[0]['name'] = $db->escape($gallery[0]['name'], 3);
	$gallery[0]['alias'] = seo::getUriAlias('gallery/pics/id_' . $uri->id, true);
	$gallery[0]['seo_keywords'] = seo::getKeywords('gallery/pics/id_' . $uri->id);
	$gallery[0]['seo_description'] = seo::getDescription('gallery/pics/id_' . $uri->id);

	breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
	breadcrumb::assign($lang->t('gallery', 'gallery'), $uri->route('acp/gallery'));
	breadcrumb::assign($gallery[0]['name']);

	if (isset($_POST['form']) === true) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($form['name']) < 3)
			$errors[] = $lang->t('gallery', 'type_in_gallery_name');
		if (CONFIG_SEO_ALIASES === true && !empty($form['alias']) && (validate::isUriSafe($form['alias']) === false || validate::uriAliasExists($form['alias'], 'gallery/pics/id_' . $uri->id)))
			$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'name' => $db->escape($form['name']),
				'user_id' => $auth->getUserId(),
			);

			$bool = $db->update('gallery', $update_values, 'id = \'' . $uri->id . '\'');
			if (CONFIG_SEO_ALIASES === true && !empty($form['alias'])) {
				seo::insertUriAlias($form['alias'], 'gallery/pics/id_' . $uri->id, $db->escape($form['seo_keywords']), $db->escape($form['seo_description']));
				require_once MODULES_DIR . 'gallery/functions.php';
				generatePictureAliases($uri->id);
			}

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/gallery');
		}
	}
	if (isset($_POST['entries']) === false && isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		getRedirectMessage();

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
				$pictures[$i]['description'] = $db->escape($pictures[$i]['description'], 3);
			}
			$tpl->assign('pictures', $pictures);
		}

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('gallery/edit_gallery.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
