<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (validate::isNumber($uri->id) && $db->countRows('*', 'static_pages', 'id = \'' . $uri->id . '\'') == '1') {
	require_once MODULES_DIR . 'static_pages/functions.php';

	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (strlen($form['title']) < 3)
			$errors[] = $lang->t('static_pages', 'title_to_short');
		if (strlen($form['text']) < 3)
			$errors[] = $lang->t('static_pages', 'text_to_short');
		if (CONFIG_SEO_ALIASES === true && !empty($form['alias']) && (!validate::isUriSafe($form['alias']) || validate::uriAliasExists($form['alias'], 'static_pages/list/id_' . $uri->id)))
			$errors[] = $lang->t('common', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'title' => $db->escape($form['title']),
				'text' => $db->escape($form['text'], 2),
				'user_id' => $auth->getUserId(),
			);

			$bool = $db->update('static_pages', $update_values, 'id = \'' . $uri->id . '\'');
			if (CONFIG_SEO_ALIASES === true && !empty($form['alias']))
				seo::insertUriAlias($form['alias'], 'static_pages/list/id_' . $uri->id, $db->escape($form['seo_keywords']), $db->escape($form['seo_description']));

			setStaticPagesCache($uri->id);

			// Aliase in der Navigation aktualisieren
			require_once MODULES_DIR . 'menu_items/functions.php';
			setMenuItemsCache();

			view::setContent(comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('acp/static_pages')));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$page = getStaticPagesCache($uri->id);
		$page[0]['text'] = $db->escape($page[0]['text'], 3);
		$page[0]['alias'] = seo::getUriAlias('static_pages/list/id_' . $uri->id);
		$page[0]['seo_keywords'] = seo::getKeywords('static_pages/list/id_' . $uri->id);
		$page[0]['seo_description'] = seo::getDescription('static_pages/list/id_' . $uri->id);

		// Datumsauswahl
		$tpl->assign('publication_period', $date->datepicker(array('start', 'end'), array($page[0]['start'], $page[0]['end'])));

		$tpl->assign('form', isset($form) ? $form : $page[0]);

		view::setContent(view::fetchTemplate('static_pages/edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
