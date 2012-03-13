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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'gallery', 'id = \'' . $uri->id . '\'') == 1) {
	$gallery = $db->select('start, end, name', 'gallery', 'id = \'' . $uri->id . '\'');
	$gallery[0]['name'] = $db->escape($gallery[0]['name'], 3);

	$tpl->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields('gallery/pics/id_' . $uri->id));

	$breadcrumb->append($gallery[0]['name']);

	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($_POST['name']) < 3)
			$errors['name'] = $lang->t('gallery', 'type_in_gallery_name');
		if (CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], 'gallery/pics/id_' . $uri->id)))
			$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => $date->timestamp($_POST['start']),
				'end' => $date->timestamp($_POST['end']),
				'name' => $db->escape($_POST['name']),
				'user_id' => $auth->getUserId(),
			);

			$bool = $db->update('gallery', $update_values, 'id = \'' . $uri->id . '\'');
			if (CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
				ACP3_SEO::insertUriAlias('gallery/pics/id_' . $uri->id, $_POST['alias'], $db->escape($_POST['seo_keywords']), $db->escape($_POST['seo_description']), (int) $_POST['seo_robots']);
				require_once MODULES_DIR . 'gallery/functions.php';
				generatePictureAliases($uri->id);
			}

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/gallery');
		}
	}
	if (isset($_POST['entries']) === false && isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		getRedirectMessage();

		$tpl->assign('gallery_id', $uri->id);

		// Datumsauswahl
		$tpl->assign('publication_period', $date->datepicker(array('start', 'end'), array($gallery[0]['start'], $gallery[0]['end'])));

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $gallery[0]);

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

		ACP3_View::setContent(ACP3_View::fetchTemplate('gallery/edit_gallery.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
