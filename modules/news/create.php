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

require_once ACP3_ROOT . 'modules/categories/functions.php';

$settings = config::output('news');

if (isset($_POST['submit'])) {
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
	if (!empty($form['uri']) && (!validate::isNumber($form['target']) || strlen($form['link_title']) < 3))
		$errors[] = $lang->t('news', 'complete_additional_hyperlink_statements');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => $date->timestamp($form['start']),
			'end' => $date->timestamp($form['end']),
			'headline' => $db->escape($form['headline']),
			'text' => $db->escape($form['text'], 2),
			'readmore' => $settings['readmore'] == 1 && isset($form['readmore']) ? 1 : 0,
			'comments' => $settings['comments'] == 1 && isset($form['comments']) ? 1 : 0,
			'category_id' => strlen($form['cat_create']) >= 3 ? categoriesCreate($form['cat_create'], 'news') : $form['cat'],
			'uri' => $db->escape($form['uri'], 2),
			'target' => $form['target'],
			'link_title' => $db->escape($form['link_title'])
		);

		$bool = $db->insert('news', $insert_values);

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/news'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', datepicker('start'));
	$tpl->assign('end_date', datepicker('end'));

	// Kategorien
	$tpl->assign('categories', categoriesList('news', '', true));

	// Weiterlesen & Kommentare
	if ($settings['readmore'] == 1 || $settings['comments'] == 1) {
		$i = 0;
		if ($settings['readmore'] == 1) {
			$options[$i]['name'] = 'readmore';
			$options[$i]['checked'] = selectEntry('readmore', '1', '0', 'checked');
			$options[$i]['lang'] = $lang->t('news', 'activate_readmore');
			$i++;
		}
		if ($settings['comments'] == 1 && modules::check('comments', 'functions') == 1) {
			$options[$i]['name'] = 'comments';
			$options[$i]['checked'] = selectEntry('comments', '1', '0', 'checked');
			$options[$i]['lang'] = $lang->t('common', 'allow_comments');
		}
		$tpl->assign('options', $options);
	}

	// Linkziel
	$target[0]['value'] = '1';
	$target[0]['selected'] = selectEntry('target', '1');
	$target[0]['lang'] = $lang->t('common', 'window_self');
	$target[1]['value'] = '2';
	$target[1]['selected'] = selectEntry('target', '2');
	$target[1]['lang'] = $lang->t('common', 'window_blank');
	$tpl->assign('target', $target);

	$tpl->assign('form', isset($form) ? $form : array('headline' => '', 'text' => '', 'uri' => '', 'link_title' => ''));

	$content = $tpl->fetch('news/create.html');
}
?>