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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	$gallery = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT start, end, title FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

	ACP3\CMS::$injector['View']->assign('SEO_FORM_FIELDS', ACP3\Core\SEO::formFields('gallery/pics/id_' . ACP3\CMS::$injector['URI']->id));

	ACP3\CMS::$injector['Breadcrumb']->append($gallery['title']);

	if (isset($_POST['submit']) === true) {
		if (ACP3\Core\Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = ACP3\CMS::$injector['Lang']->t('system', 'select_date');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3\CMS::$injector['Lang']->t('gallery', 'type_in_gallery_title');
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3\Core\Validate::isUriSafe($_POST['alias']) === false || ACP3\Core\Validate::uriAliasExists($_POST['alias'], 'gallery/pics/id_' . ACP3\CMS::$injector['URI']->id)))
			$errors['alias'] = ACP3\CMS::$injector['Lang']->t('system', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => ACP3\CMS::$injector['Date']->toSQL($_POST['start']),
				'end' => ACP3\CMS::$injector['Date']->toSQL($_POST['end']),
				'title' => ACP3\Core\Functions::str_encode($_POST['title']),
				'user_id' => ACP3\CMS::$injector['Auth']->getUserId(),
			);

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'gallery', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias'])) {
				ACP3\Core\SEO::insertUriAlias('gallery/pics/id_' . ACP3\CMS::$injector['URI']->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);
				require_once MODULES_DIR . 'gallery/functions.php';
				generatePictureAliases(ACP3\CMS::$injector['URI']->id);
			}

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery');
		}
	}
	if (isset($_POST['entries']) === false && isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		ACP3\Core\Functions::getRedirectMessage();

		ACP3\CMS::$injector['View']->assign('gallery_id', ACP3\CMS::$injector['URI']->id);

		// Datumsauswahl
		ACP3\CMS::$injector['View']->assign('publication_period', ACP3\CMS::$injector['Date']->datepicker(array('start', 'end'), array($gallery['start'], $gallery['end'])));

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $gallery);

		$pictures = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, pic, file, description FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ? ORDER BY pic ASC', array(ACP3\CMS::$injector['URI']->id));
		$c_pictures = count($pictures);

		if ($c_pictures > 0) {
			$can_delete = ACP3\Core\Modules::check('gallery', 'acp_delete_picture');
			$config = array(
				'element' => '#acp-table',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));

			for ($i = 0; $i < $c_pictures; ++$i) {
				$pictures[$i]['first'] = $i == 0 ? true : false;
				$pictures[$i]['last'] = $i == $c_pictures - 1 ? true : false;
			}
			ACP3\CMS::$injector['View']->assign('pictures', $pictures);
			ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
			ACP3\CMS::$injector['View']->assign('can_order', ACP3\Core\Modules::check('gallery', 'acp_order'));
			ACP3\CMS::$injector['View']->assign('can_edit_picture', ACP3\Core\Modules::check('gallery', 'acp_edit_picture'));
		}

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
