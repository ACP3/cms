<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (validate::isNumber($uri->id) && $db->select('id', 'pages', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	// Funktionen einbinden
	include_once ACP3_ROOT . 'modules/pages/functions.php';

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (!validate::date($form['start'], $form['end']))
			$errors[] = $lang->t('common', 'select_date');
		if (!validate::isNumber($form['mode']))
			$errors[] = $lang->t('pages', 'select_static_hyperlink');
		if (!validate::isNumber($form['blocks']))
			$errors[] = $lang->t('pages', 'select_block');
		if (!empty($form['blocks']) && !validate::isNumber($form['sort']))
			$errors[] = $lang->t('pages', 'type_in_chronology');
		if (strlen($form['title']) < 3)
			$errors[] = $lang->t('pages', 'title_to_short');
		if ($form['mode'] == '1' && !empty($form['parent']) && !validate::isNumber($form['parent']))
			$errors[] = $lang->t('pages', 'select_superior_page');
		if ($form['mode'] == '1' && validate::isNumber($form['parent']) && ($db->select('id', 'pages', "id != '" . $uri->id . "' AND mode='1' AND parent='0'", 0, 0, 0, 1) == 0) || $form['parent'] == $uri->id || parentCheck($uri->id, $form['parent']))
			$errors[] = $lang->t('pages', 'superior_page_not_allowed');
		if ($form['mode'] == '1' && strlen($form['text']) < 3)
			$errors[] = $lang->t('pages', 'text_to_short');
		if (($form['mode'] == '2' || $form['mode'] == '3') && (empty($form['uri']) || !validate::isNumber($form['target'])))
			$errors[] = $lang->t('pages', 'type_in_uri_and_target');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			if ($form['mode'] == '1') {
				$form['uri'] = '';
				$form['target'] = '';
			} else {
				$form['text'] = '';
			}

			$update_values = array(
				'start' => $date->timestamp($form['start']),
				'end' => $date->timestamp($form['end']),
				'mode' => $form['mode'],
				'parent' => $form['parent'],
				'block_id' => $form['blocks'],
				'sort' => $form['sort'],
				'title' => $db->escape($form['title']),
				'uri' => $db->escape($form['uri'], 2),
				'target' => $form['target'],
				'text' => $db->escape($form['text'], 2),
			);

			$bool = $db->update('pages', $update_values, 'id = \'' . $uri->id . '\'');

			cache::create('pages_list_id_' . $uri->id, $db->select('mode, uri, text', 'pages', 'id = \'' . $uri->id . '\''));
			generatePagesCache();

			$content = comboBox($bool ? $lang->t('pages', 'edit_success') : $lang->t('pages', 'edit_error'), uri('acp/pages'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {

		$page = $db->select('id, start, end, mode, parent, block_id, sort, title, uri, target, text', 'pages', 'id = \'' . $uri->id . '\'');
		$page[0]['text'] = $db->escape($page[0]['text'], 3);
		$page[0]['uri'] = $db->escape($page[0]['uri'], 3);

		// Datumsauswahl
		$tpl->assign('start_date', datepicker('start', $page[0]['start']));
		$tpl->assign('end_date', datepicker('end', $page[0]['end']));

		$mode[0]['value'] = 1;
		$mode[0]['selected'] = selectEntry('mode', '1', $page[0]['mode']);
		$mode[0]['lang'] = $lang->t('pages', 'static_page');
		$mode[1]['value'] = 2;
		$mode[1]['selected'] = selectEntry('mode', '2', $page[0]['mode']);
		$mode[1]['lang'] = $lang->t('pages', 'dynamic_page');
		$mode[2]['value'] = 3;
		$mode[2]['selected'] = selectEntry('mode', '3', $page[0]['mode']);
		$mode[2]['lang'] = $lang->t('pages', 'hyperlink');
		$tpl->assign('mode', $mode);

		$blocks = $db->select('id, title', 'pages_blocks', 0, 'title ASC, id ASC');
		$c_blocks = count($blocks);

		for ($i = 0; $i < $c_blocks; ++$i) {
			$blocks[$i]['selected'] = selectEntry('blocks', $blocks[$i]['id'], $page[0]['block_id']);
		}
		$blocks[$c_blocks]['id'] = '0';
		$blocks[$c_blocks]['index_name'] = 'dot_display';
		$blocks[$c_blocks]['selected'] = selectEntry('block', '0', $page[0]['block_id']);
		$blocks[$c_blocks]['title'] = $lang->t('pages', 'do_not_display');
		$tpl->assign('blocks', $blocks);

		$target[0]['value'] = 1;
		$target[0]['selected'] = selectEntry('target', '1', $page[0]['target']);
		$target[0]['lang'] = $lang->t('common', 'window_self');
		$target[1]['value'] = 2;
		$target[1]['selected'] = selectEntry('target', '2', $page[0]['target']);
		$target[1]['lang'] = $lang->t('common', 'window_blank');
		$tpl->assign('target', $target);

		$tpl->assign('form', isset($form) ? $form : $page[0]);

		$tpl->assign('pages_list', pagesList(0, $page[0]['parent'], $page[0]['id']));

		$content = $tpl->fetch('pages/edit.html');
	}
} else {
	redirect('errors/404');
}
?>