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
if (!$modules->check(0, 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];

		if (!$validate->date($form))
			$errors[] = lang('common', 'select_date');
		if (strlen($form['headline']) < 3)
			$errors[] = lang('news', 'headline_to_short');
		if (strlen($form['text']) < 3)
			$errors[] = lang('news', 'text_to_short');
		if (!ereg('[0-9]', $form['cat']) || ereg('[0-9]', $form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
			$errors[] = lang('news', 'select_category');
		if (!empty($form['uri']) && (!ereg('[0-9]', $form['target']) || strlen($form['link_title']) < 3))
			$errors[] = lang('news', 'complete_additional_hyperlink_statements');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$insert_values = array(
				'id' => '',
				'start' => $start_date,
				'end' => $end_date,
				'headline' => $db->escape($form['headline']),
				'text' => $db->escape($form['text'], 2),
				'cat' => $form['cat'],
				'uri' => $db->escape($form['uri'], 2),
				'target' => $form['target'],
				'link_title' => $db->escape($form['link_title'])
			);

			$bool = $db->insert('news', $insert_values);

			$content = combo_box($bool ? lang('news', 'create_success') : lang('news', 'create_error'), uri('acp/news'));
		}
		break;
	case 'edit':
		$form = $_POST['form'];

		if (!$validate->date($form))
			$errors[] = lang('common', 'select_date');
		if (strlen($form['headline']) < 3)
			$errors[] = lang('news', 'headline_to_short');
		if (strlen($form['text']) < 3)
			$errors[] = lang('news', 'text_to_short');
		if (!ereg('[0-9]', $form['cat']) || ereg('[0-9]', $form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
			$errors[] = lang('news', 'select_category');
		if (!empty($form['uri']) && (!ereg('[0-9]', $form['target']) || strlen($form['link_title']) < 3))
			$errors[] = lang('news', 'complete_additional_hyperlink_statements');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'headline' => $db->escape($form['headline']),
				'text' => $db->escape($form['text'], 2),
				'cat' => $form['cat'],
				'uri' => $db->escape($form['uri'], 2),
				'target' => $form['target'],
				'link_title' => $db->escape($form['link_title'])
			);

			$bool = $db->update('news', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('news_details_id_' . $modules->id, $db->select('id, start, headline, text, cat, uri, target, link_title', 'news', 'id = \'' . $modules->id . '\''));

			$content = combo_box($bool ? lang('news', 'edit_success') : lang('news', 'edit_error'), uri('acp/news'));
		}
		break;
	case 'delete':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && ereg('^([0-9|]+)$', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('news', 'confirm_delete'), uri('acp/news/adm_list/action_delete/entries_' . $marked_entries), uri('acp/news'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			$bool2 = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'news', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					$bool = $db->delete('news', 'id = \'' . $entry . '\'');
					$bool2 = $db->delete('comments', 'module = \'news\' AND entry_id = \'' . $entry . '\'');
					// News Cache löschen
					$cache->delete('news_details_id_' . $entry);
				}
			}
			$content = combo_box($bool && $bool2 ? lang('news', 'delete_success') : lang('news', 'delete_error'), uri('acp/news'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>