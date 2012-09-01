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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'articles', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'articles/functions.php';

	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = $lang->t('articles', 'title_to_short');
		if (strlen($_POST['text']) < 3)
			$errors['text'] = $lang->t('articles', 'text_to_short');
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], 'articles/list/id_' . $uri->id) === true))
			$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => $_POST['start'],
				'end' => $_POST['end'],
				'title' => $db->escape($_POST['title']),
				'text' => $db->escape($_POST['text'], 2),
				'user_id' => $auth->getUserId(),
			);

			$bool = $db->update('articles', $update_values, 'id = \'' . $uri->id . '\'');
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
				ACP3_SEO::insertUriAlias('articles/list/id_' . $uri->id, $_POST['alias'], $db->escape($_POST['seo_keywords']), $db->escape($_POST['seo_description']), (int) $_POST['seo_robots']);

			setArticlesCache($uri->id);

			// Aliase in der Navigation aktualisieren
			require_once MODULES_DIR . 'menus/functions.php';
			setMenuItemsCache();

			$session->unsetFormToken();

			setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/articles');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$page = getArticlesCache($uri->id);
		$page[0]['text'] = $db->escape($page[0]['text'], 3);

		// Datumsauswahl
		$tpl->assign('publication_period', $date->datepicker(array('start', 'end'), array($page[0]['start'], $page[0]['end'])));

		$tpl->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields('articles/list/id_' . $uri->id));

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $page[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('articles/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
