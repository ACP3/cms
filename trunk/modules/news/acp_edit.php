<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'news', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'categories/functions.php';

	$settings = ACP3_Config::getSettings('news');

	if (isset($_POST['submit']) === true) {
		if (ACP3_Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($_POST['headline']) < 3)
			$errors['headline'] = $lang->t('news', 'headline_to_short');
		if (strlen($_POST['text']) < 3)
			$errors['text'] = $lang->t('news', 'text_to_short');
		if (strlen($_POST['cat_create']) < 3 && categoriesCheck($_POST['cat']) === false)
			$errors['cat'] = $lang->t('news', 'select_category');
		if (strlen($_POST['cat_create']) >= 3 && categoriesCheckDuplicate($_POST['cat_create'], 'news') === true)
			$errors['cat-create'] = $lang->t('categories', 'category_already_exists');
		if (!empty($_POST['link_title']) && (empty($_POST['uri']) || ACP3_Validate::isNumber($_POST['target']) === false))
			$errors[] = $lang->t('news', 'complete_additional_hyperlink_statements');
		if (CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3_Validate::isUriSafe($_POST['alias']) === false || ACP3_Validate::uriAliasExists($_POST['alias'], 'news/details/id_' . $uri->id) === true))
			$errors['alias'] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => $_POST['start'],
				'end' => $_POST['end'],
				'headline' => $db->escape($_POST['headline']),
				'text' => $db->escape($_POST['text'], 2),
				'readmore' => $settings['readmore'] == 1 && isset($_POST['readmore']) ? 1 : 0,
				'comments' => $settings['comments'] == 1 && isset($_POST['comments']) ? 1 : 0,
				'category_id' => strlen($_POST['cat_create']) >= 3 ? categoriesCreate($_POST['cat_create'], 'news') : $_POST['cat'],
				'uri' => $db->escape($_POST['uri'], 2),
				'target' => $_POST['target'],
				'link_title' => $db->escape($_POST['link_title']),
				'user_id' => $auth->getUserId(),
			);

			$bool = $db->update('news', $update_values, 'id = \'' . $uri->id . '\'');
			if (CONFIG_SEO_ALIASES === true)
				ACP3_SEO::insertUriAlias('news/details/id_' . $uri->id, $_POST['alias'], $db->escape($_POST['seo_keywords']), $db->escape($_POST['seo_description']), (int) $_POST['seo_robots']);

			require_once MODULES_DIR . 'news/functions.php';
			setNewsCache($uri->id);

			$session->unsetFormToken();

			setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/news');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$news = $db->select('start, end, headline, text, readmore, comments, category_id, uri, target, link_title', 'news', 'id = \'' . $uri->id . '\'');
		$news[0]['headline'] = $db->escape($news[0]['headline'], 3);
		$news[0]['text'] = $db->escape($news[0]['text'], 3);

		// Datumsauswahl
		$tpl->assign('publication_period', $date->datepicker(array('start', 'end'), array($news[0]['start'], $news[0]['end'])));

		// Kategorien
		$tpl->assign('categories', categoriesList('news', $news[0]['category_id'], true));

		// Weiterlesen & Kommentare
		if ($settings['readmore'] == 1 || $settings['comments'] == 1) {
			$i = 0;
			$options = array();
			if ($settings['readmore'] == 1) {
				$options[$i]['name'] = 'readmore';
				$options[$i]['checked'] = selectEntry('readmore', '1', $news[0]['readmore'], 'checked');
				$options[$i]['lang'] = $lang->t('news', 'activate_readmore');
				$i++;
			}
			if ($settings['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
				$options[$i]['name'] = 'comments';
				$options[$i]['checked'] = selectEntry('comments', '1', $news[0]['comments'], 'checked');
				$options[$i]['lang'] = $lang->t('common', 'allow_comments');
			}
			$tpl->assign('options', $options);
		}

		// Linkziel
		$target = array();
		$target[0]['value'] = '1';
		$target[0]['selected'] = selectEntry('target', '1', $news[0]['target']);
		$target[0]['lang'] = $lang->t('common', 'window_self');
		$target[1]['value'] = '2';
		$target[1]['selected'] = selectEntry('target', '2', $news[0]['target']);
		$target[1]['lang'] = $lang->t('common', 'window_blank');
		$tpl->assign('target', $target);

		$tpl->assign('SEO_FORM_FIELDS', ACP3_SEO::formFields('news/details/id_' . $uri->id));

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $news[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('news/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}