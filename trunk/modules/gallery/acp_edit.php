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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	$gallery = ACP3_CMS::$db2->fetchAssoc('SELECT start, end, title FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3_CMS::$uri->id));

	ACP3_CMS::$view->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields('gallery/pics/id_' . ACP3_CMS::$uri->id));

	ACP3_CMS::$breadcrumb->append($gallery['title']);

	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = ACP3_CMS::$lang->t('system', 'select_date');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3_CMS::$lang->t('gallery', 'type_in_gallery_title');
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], 'gallery/pics/id_' . ACP3_CMS::$uri->id)))
			$errors['alias'] = ACP3_CMS::$lang->t('system', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => ACP3_CMS::$date->toSQL($_POST['start']),
				'end' => ACP3_CMS::$date->toSQL($_POST['end']),
				'title' => str_encode($_POST['title']),
				'user_id' => ACP3_CMS::$auth->getUserId(),
			);

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'gallery', $update_values, array('id' => ACP3_CMS::$uri->id));
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
				ACP3_SEO::insertUriAlias('gallery/pics/id_' . ACP3_CMS::$uri->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);
				require_once MODULES_DIR . 'gallery/functions.php';
				generatePictureAliases(ACP3_CMS::$uri->id);
			}

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
		}
	}
	if (isset($_POST['entries']) === false && isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		getRedirectMessage();

		ACP3_CMS::$view->assign('gallery_id', ACP3_CMS::$uri->id);

		// Datumsauswahl
		ACP3_CMS::$view->assign('publication_period', ACP3_CMS::$date->datepicker(array('start', 'end'), array($gallery['start'], $gallery['end'])));

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $gallery);

		$pictures = ACP3_CMS::$db2->fetchAll('SELECT id, pic, file, description FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ? ORDER BY pic ASC', array(ACP3_CMS::$uri->id));
		$c_pictures = count($pictures);

		if ($c_pictures > 0) {
			$can_delete = ACP3_Modules::check('gallery', 'acp_delete_picture');
			$config = array(
				'element' => '#acp-table',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			ACP3_CMS::setContent(datatable($config));

			for ($i = 0; $i < $c_pictures; ++$i) {
				$pictures[$i]['first'] = $i == 0 ? true : false;
				$pictures[$i]['last'] = $i == $c_pictures - 1 ? true : false;
			}
			ACP3_CMS::$view->assign('pictures', $pictures);
			ACP3_CMS::$view->assign('can_delete', $can_delete);
			ACP3_CMS::$view->assign('can_order', ACP3_Modules::check('gallery', 'acp_order'));
			ACP3_CMS::$view->assign('can_edit_picture', ACP3_Modules::check('gallery', 'acp_edit_picture'));
		}

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::appendContent(ACP3_CMS::$view->fetchTemplate('gallery/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
