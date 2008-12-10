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

if (validate::isNumber($uri->id) && $db->select('COUNT(id)', 'news', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($form['headline']) < 3)
			$errors[] = $lang->t('news', 'headline_to_short');
		if (strlen($form['text']) < 3)
			$errors[] = $lang->t('news', 'text_to_short');
		if (!validate::isNumber($form['cat']) || validate::isNumber($form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
			$errors[] = $lang->t('news', 'select_category');
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
				'readmore' => isset($form['readmore']) ? 1 : 0,
				'comments' => isset($form['comments']) ? 1 : 0,
				'category_id' => $form['cat'],
				'uri' => $db->escape($form['uri'], 2),
				'target' => $form['target'],
				'link_title' => $db->escape($form['link_title'])
			);

			$bool = $db->update('news', $update_values, 'id = \'' . $uri->id . '\'');
			setNewsCache($uri->id);

			$content = comboBox($bool ? $lang->t('news', 'edit_success') : $lang->t('news', 'edit_error'), uri('acp/news'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$news = $db->select('start, end, headline, text, readmore, comments, category_id, uri, target, link_title', 'news', 'id = \'' . $uri->id . '\'');
		$news[0]['text'] = $db->escape($news[0]['text'], 3);

		// Datumsauswahl
		$tpl->assign('start_date', datepicker('start', $news[0]['start']));
		$tpl->assign('end_date', datepicker('end', $news[0]['end']));

		// Kategorien
		if (modules::check('categories', 'functions')) {
			include_once ACP3_ROOT . 'modules/categories/functions.php';
			$tpl->assign('categories', categoriesList('news', $news[0]['category_id']));
		}

		// Weiterlesen & Kommentare
		$options[0]['id'] = 'readmore';
		$options[0]['name'] = 'readmore';
		$options[0]['checked'] = selectEntry('readmore', '1', $news[0]['readmore'], 'checked');
		$options[0]['lang'] = $lang->t('news', 'activate_readmore');
		$options[1]['id'] = 'comments';
		$options[1]['name'] = 'comments';
		$options[1]['checked'] = selectEntry('comments', '1', $news[0]['comments'], 'checked');
		$options[1]['lang'] = $lang->t('news', 'allow_comments');
		$tpl->assign('options', $options);

		// Linkziel
		$target[0]['value'] = '1';
		$target[0]['selected'] = selectEntry('target', '1', $news[0]['target']);
		$target[0]['lang'] = $lang->t('common', 'window_self');
		$target[1]['value'] = '2';
		$target[1]['selected'] = selectEntry('target', '2', $news[0]['target']);
		$target[1]['lang'] = $lang->t('common', 'window_blank');
		$tpl->assign('target', $target);

		$tpl->assign('form', isset($form) ? $form : $news[0]);

		$content = $tpl->fetch('news/edit.html');
	}
} else {
	redirect('errors/404');
}
?>