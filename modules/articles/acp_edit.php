<?php
/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'articles', 'id = \'' . ACP3_CMS::$uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'articles/functions.php';

	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = ACP3_CMS::$lang->t('common', 'select_date');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3_CMS::$lang->t('articles', 'title_to_short');
		if (strlen($_POST['text']) < 3)
			$errors['text'] = ACP3_CMS::$lang->t('articles', 'text_to_short');
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], 'articles/list/id_' . ACP3_CMS::$uri->id) === true))
			$errors['alias'] = ACP3_CMS::$lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => $_POST['start'],
				'end' => $_POST['end'],
				'title' => ACP3_CMS::$db->escape($_POST['title']),
				'text' => ACP3_CMS::$db->escape($_POST['text'], 2),
				'user_id' => ACP3_CMS::$auth->getUserId(),
			);

			$bool = ACP3_CMS::$db->update('articles', $update_values, 'id = \'' . ACP3_CMS::$uri->id . '\'');
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
				ACP3_SEO::insertUriAlias('articles/list/id_' . ACP3_CMS::$uri->id, $_POST['alias'], ACP3_CMS::$db->escape($_POST['seo_keywords']), ACP3_CMS::$db->escape($_POST['seo_description']), (int) $_POST['seo_robots']);

			setArticlesCache(ACP3_CMS::$uri->id);

			// Aliase in der Navigation aktualisieren
			require_once MODULES_DIR . 'menus/functions.php';
			setMenuItemsCache();

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/articles');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$page = getArticlesCache(ACP3_CMS::$uri->id);
		$page[0]['text'] = ACP3_CMS::$db->escape($page[0]['text'], 3);

		// Datumsauswahl
		ACP3_CMS::$view->assign('publication_period', ACP3_CMS::$date->datepicker(array('start', 'end'), array($page[0]['start'], $page[0]['end'])));

		ACP3_CMS::$view->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields('articles/list/id_' . ACP3_CMS::$uri->id));

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $page[0]);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('articles/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
