<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (validate::isNumber($uri->id) && $db->countRows('*', 'news', 'id = \'' . $uri->id . '\'') == '1') {
	require_once MODULES_DIR . 'categories/functions.php';

	$settings = config::getModuleSettings('news');

	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($form['headline']) < 3)
			$errors[] = $lang->t('news', 'headline_to_short');
		if (strlen($form['text']) < 3)
			$errors[] = $lang->t('news', 'text_to_short');
		if (strlen($form['cat_create']) < 3 && !categoriesCheck($form['cat']))
			$errors[] = $lang->t('news', 'select_category');
		if (strlen($form['cat_create']) >= 3 && categoriesCheckDuplicate($form['cat_create'], 'news'))
			$errors[] = $lang->t('categories', 'category_already_exists');
		if (!validate::isUriSafe($form['alias']) || validate::UriAliasExists($form['alias'], 'news/details/id_' . $uri->id))
			$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');
		if (!empty($form['uri']) && (!validate::isNumber($form['target']) || strlen($form['link_title']) < 3))
			$errors[] = $lang->t('news', 'complete_additional_hyperlink_statements');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'headline' => $db->escape($form['headline']),
				'text' => $db->escape($form['text'], 2),
				'readmore' => $settings['readmore'] == 1 && isset($form['readmore']) ? 1 : 0,
				'comments' => $settings['comments'] == 1 && isset($form['comments']) ? 1 : 0,
				'category_id' => strlen($form['cat_create']) >= 3 ? categoriesCreate($form['cat_create'], 'news') : $form['cat'],
				'uri' => $db->escape($form['uri'], 2),
				'target' => $form['target'],
				'link_title' => $db->escape($form['link_title']),
				'user_id' => $auth->getUserId(),
			);

			$bool = $db->update('news', $update_values, 'id = \'' . $uri->id . '\'');
			$bool2 = seo::insertUriAlias($form['alias'], 'news/details/id_' . $uri->id, $db->escape($form['seo_keywords']), $db->escape($form['seo_description']));

			require_once MODULES_DIR . 'news/functions.php';
			setNewsCache($uri->id);

			$content = comboBox($bool && $bool2 ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('acp/news'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$news = $db->select('start, end, headline, text, readmore, comments, category_id, uri, target, link_title', 'news', 'id = \'' . $uri->id . '\'');
		$news[0]['text'] = $db->escape($news[0]['text'], 3);
		$news[0]['alias'] = seo::getUriAlias('news/details/id_' . $uri->id);
		$news[0]['seo_keywords'] = seo::getKeywordsOrDescription('news/details/id_' . $uri->id);
		$news[0]['seo_description'] = seo::getKeywordsOrDescription('news/details/id_' . $uri->id, 'description');

		// Datumsauswahl
		$tpl->assign('publication_period', $date->datepicker(array('start', 'end'), array($news[0]['start'], $news[0]['end'])));

		// Kategorien
		$tpl->assign('categories', categoriesList('news', $news[0]['category_id'], true));

		// Weiterlesen & Kommentare
		if ($settings['readmore'] == 1 || $settings['comments'] == 1) {
			$i = 0;
			if ($settings['readmore'] == 1) {
				$options[$i]['name'] = 'readmore';
				$options[$i]['checked'] = selectEntry('readmore', '1', $news[0]['readmore'], 'checked');
				$options[$i]['lang'] = $lang->t('news', 'activate_readmore');
				$i++;
			}
			if ($settings['comments'] == 1 && modules::check('comments', 'functions') == 1) {
				$options[$i]['name'] = 'comments';
				$options[$i]['checked'] = selectEntry('comments', '1', $news[0]['comments'], 'checked');
				$options[$i]['lang'] = $lang->t('common', 'allow_comments');
			}
			$tpl->assign('options', $options);
		}

		// Linkziel
		$target[0]['value'] = '1';
		$target[0]['selected'] = selectEntry('target', '1', $news[0]['target']);
		$target[0]['lang'] = $lang->t('common', 'window_self');
		$target[1]['value'] = '2';
		$target[1]['selected'] = selectEntry('target', '2', $news[0]['target']);
		$target[1]['lang'] = $lang->t('common', 'window_blank');
		$tpl->assign('target', $target);

		$tpl->assign('form', isset($form) ? $form : $news[0]);

		$content = modules::fetchTemplate('news/edit.html');
	}
} else {
	$uri->redirect('errors/404');
}