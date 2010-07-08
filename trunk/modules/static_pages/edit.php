<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (validate::isNumber($uri->id) && $db->countRows('*', 'static_pages', 'id = \'' . $uri->id . '\'') == '1') {
	require_once ACP3_ROOT . 'modules/static_pages/functions.php';

	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($form['title']) < 3)
			$errors[] = $lang->t('static_pages', 'title_to_short');
		if (strlen($form['text']) < 3)
			$errors[] = $lang->t('static_pages', 'text_to_short');
		if (!validate::isUriSafe($form['alias']) || validate::UriAliasExists($form['alias']))
			$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'title' => db::escape($form['title']),
				'text' => db::escape($form['text'], 2),
			);

			$bool = $db->update('static_pages', $update_values, 'id = \'' . $uri->id . '\'');
			$bool2 = $uri->insertUriAlias($form['alias'], 'static_pages/list/id_' . $uri->id);

			setStaticPagesCache($uri->id);

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('acp/static_pages'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$page = getStaticPagesCache($uri->id);
		$page[0]['text'] = db::escape($page[0]['text'], 3);
		$page[0]['alias'] = $uri->getUriAlias('static_pages/list/id_' . $uri->id);

		// Datumsauswahl
		$tpl->assign('publication_period', datepicker(array('start', 'end'), array($page[0]['start'], $page[0]['end'])));

		$tpl->assign('form', isset($form) ? $form : $page[0]);

		$content = modules::fetchTemplate('static_pages/edit.html');
	}
} else {
	redirect('errors/404');
}
