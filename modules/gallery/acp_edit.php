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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'gallery', 'id = \'' . ACP3_CMS::$uri->id . '\'') == 1) {
	$gallery = ACP3_CMS::$db->select('start, end, name', 'gallery', 'id = \'' . ACP3_CMS::$uri->id . '\'');
	$gallery[0]['name'] = ACP3_CMS::$db->escape($gallery[0]['name'], 3);

	ACP3_CMS::$view->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields('gallery/pics/id_' . ACP3_CMS::$uri->id));

	ACP3_CMS::$breadcrumb->append($gallery[0]['name']);

	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = ACP3_CMS::$lang->t('common', 'select_date');
		if (strlen($_POST['name']) < 3)
			$errors['name'] = ACP3_CMS::$lang->t('gallery', 'type_in_gallery_name');
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], 'gallery/pics/id_' . ACP3_CMS::$uri->id)))
			$errors['alias'] = ACP3_CMS::$lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => $_POST['start'],
				'end' => $_POST['end'],
				'name' => ACP3_CMS::$db->escape($_POST['name']),
				'user_id' => ACP3_CMS::$auth->getUserId(),
			);

			$bool = ACP3_CMS::$db->update('gallery', $update_values, 'id = \'' . ACP3_CMS::$uri->id . '\'');
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
				ACP3_SEO::insertUriAlias('gallery/pics/id_' . ACP3_CMS::$uri->id, $_POST['alias'], ACP3_CMS::$db->escape($_POST['seo_keywords']), ACP3_CMS::$db->escape($_POST['seo_description']), (int) $_POST['seo_robots']);
				require_once MODULES_DIR . 'gallery/functions.php';
				generatePictureAliases(ACP3_CMS::$uri->id);
			}

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
		}
	}
	if (isset($_POST['entries']) === false && isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		getRedirectMessage();

		ACP3_CMS::$view->assign('gallery_id', ACP3_CMS::$uri->id);

		// Datumsauswahl
		ACP3_CMS::$view->assign('publication_period', ACP3_CMS::$date->datepicker(array('start', 'end'), array($gallery[0]['start'], $gallery[0]['end'])));

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $gallery[0]);

		$pictures = ACP3_CMS::$db->select('id, pic, file, description', 'gallery_pictures', 'gallery_id = \'' . ACP3_CMS::$uri->id . '\'', 'pic ASC', POS, ACP3_CMS::$auth->entries);
		$c_pictures = count($pictures);

		if ($c_pictures > 0) {
			ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'gallery_pictures', 'gallery_id = \'' . ACP3_CMS::$uri->id . '\'')));
			for ($i = 0; $i < $c_pictures; ++$i) {
				$pictures[$i]['first'] = $i == 0 ? true : false;
				$pictures[$i]['last'] = $i == $c_pictures - 1 ? true : false;
				$pictures[$i]['description'] = ACP3_CMS::$db->escape($pictures[$i]['description'], 3);
			}
			ACP3_CMS::$view->assign('pictures', $pictures);
			ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('gallery', 'acp_delete_picture'));
			ACP3_CMS::$view->assign('can_order', ACP3_Modules::check('gallery', 'acp_order'));
			ACP3_CMS::$view->assign('can_edit_picture', ACP3_Modules::check('gallery', 'acp_edit_picture'));
		}

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('gallery/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
