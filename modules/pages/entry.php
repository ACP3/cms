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
if (!$modules->check(0, 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];
		$i = 0;

		if (!$validate->date($form))
			$errors[$i++] = lang('common', 'select_date');
		if (!ereg('[0-9]', $form['mode']))
			$errors[$i++] = lang('pages', 'select_static_hyperlink');
		if (!ereg('[0-9]', $form['blocks']))
			$errors[$i++] = lang('pages', 'select_block');
		if (!empty($form['blocks']) && !ereg('[0-9]', $form['sort']))
			$errors[$i++] = lang('pages', 'type_in_chronology');
		if (strlen($form['title']) < 3)
			$errors[$i++] = lang('pages', 'title_to_short');
		if ($form['mode'] == '1' && (!empty($form['uri']) || ereg('[0-9]', $form['target'])))
			$errors[$i++] = lang('pages', 'uri_and_target_not_allowed');
		if ($form['mode'] == '1' && !empty($form['parent']) && !ereg('[0-9]', $form['parent']))
			$errors[$i++] = lang('pages', 'select_superior_page');
		if ($form['mode'] == '1' && strlen($form['text']) < 3)
			$errors[$i++] = lang('pages', 'text_to_short');
		if (($form['mode'] == '2' || $form['mode'] == '3') && (!empty($form['text']) || ereg('[0-9]', $form['parent'])))
			$errors[$i++] = lang('pages', 'superior_page_and_text_not_allowed');
		if (($form['mode'] == '2' || $form['mode'] == '3') && (empty($form['uri']) || !ereg('[0-9]', $form['target'])))
			$errors[$i++] = lang('pages', 'type_in_uri_and_target');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$insert_values = array(
				'id' => '',
				'start' => $start_date,
				'end' => $end_date,
				'mode' => $form['mode'],
				'parent' => $form['parent'],
				'block_id' => $form['blocks'],
				'sort' => $form['sort'],
				'title' => $db->escape($form['title']),
				'uri' => $db->escape($form['uri'], 2),
				'target' => $form['target'],
				'text' => $db->escape($form['text'], 2),
			);

			$bool = $db->insert('pages', $insert_values);

			$cache->create('pages', $db->select('p.id, p.start, p.end, p.mode, p.title, p.uri, p.target, b.index_name AS block_name', 'pages AS p, ' . CONFIG_DB_PRE . 'pages_blocks AS b', 'p.block_id != \'0\' AND p.block_id = b.id', 'p.sort ASC, p.title ASC'));

			$content = combo_box($bool ? lang('pages', 'create_success') : lang('pages', 'create_error'), uri('acp/pages'));
		}
		break;
	case 'edit':
		include_once 'modules/pages/functions.php';
		$form = $_POST['form'];
		$i = 0;

		if (!$validate->date($form))
			$errors[$i++] = lang('common', 'select_date');
		if (!ereg('[0-9]', $form['mode']))
			$errors[$i++] = lang('pages', 'select_static_hyperlink');
		if (!ereg('[0-9]', $form['blocks']))
			$errors[$i++] = lang('pages', 'select_block');
		if (!empty($form['blocks']) && !ereg('[0-9]', $form['sort']))
			$errors[$i++] = lang('pages', 'type_in_chronology');
		if (strlen($form['title']) < 3)
			$errors[$i++] = lang('pages', 'title_to_short');
		if ($form['mode'] == '1' && (!empty($form['uri']) || ereg('[0-9]', $form['target'])))
			$errors[$i++] = lang('pages', 'uri_and_target_not_allowed');
		if ($form['mode'] == '1' && !empty($form['parent']) && !ereg('[0-9]', $form['parent']))
			$errors[$i++] = lang('pages', 'select_superior_page');
		if ($form['mode'] == '1' && ereg('[0-9]', $form['parent']) && ($db->select('id', 'pages', "id != '" . $modules->id . "' AND mode='1' AND parent='0'", 0, 0, 0, 1) == 0) || $form['parent'] == $modules->id || parent_check($modules->id, $form['parent']))
			$errors[$i++] = lang('pages', 'superior_page_not_allowed');
		if ($form['mode'] == '1' && strlen($form['text']) < 3)
			$errors[$i++] = lang('pages', 'text_to_short');
		if (($form['mode'] == '2' || $form['mode'] == '3') && (!empty($form['text']) || ereg('[0-9]', $form['parent'])))
			$errors[$i++] = lang('pages', 'superior_page_and_text_not_allowed');
		if (($form['mode'] == '2' || $form['mode'] == '3') && (empty($form['uri']) || !ereg('[0-9]', $form['target'])))
			$errors[$i++] = lang('pages', 'type_in_uri_and_target');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'mode' => $form['mode'],
				'parent' => $form['parent'],
				'block_id' => $form['blocks'],
				'sort' => $form['sort'],
				'title' => $db->escape($form['title']),
				'uri' => $db->escape($form['uri'], 2),
				'target' => $form['target'],
				'text' => $db->escape($form['text'], 2),
			);

			$bool = $db->update('pages', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('pages', $db->select('p.id, p.start, p.end, p.mode, p.title, p.uri, p.target, b.index_name AS block_name', 'pages AS p, ' . CONFIG_DB_PRE . 'pages_blocks AS b', 'p.block_id != \'0\' AND p.block_id = b.id', 'p.sort ASC, p.title ASC'));
			$cache->create('pages_list_id_' . $modules->id, $db->select('mode, uri, text', 'pages', 'id = \'' . $modules->id . '\''));

			$content = combo_box($bool ? lang('pages', 'edit_success') : lang('pages', 'edit_error'), uri('acp/pages'));
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
			$content = combo_box(lang('pages', 'confirm_delete'), uri('acp/pages/adm_list/action_delete/entries_' . $marked_entries), uri('acp/pages'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'pages', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					$bool = $db->delete('pages', 'id = \'' . $entry . '\'');

					$cache->delete('pages_list_id_' . $entry);
				}
			}

			$cache->create('pages', $db->select('p.id, p.start, p.end, p.mode, p.title, p.uri, p.target, b.index_name AS block_name', 'pages AS p, ' . CONFIG_DB_PRE . 'pages_blocks AS b', 'p.block_id != \'0\' AND p.block_id = b.id', 'p.sort ASC, p.title ASC'));

			$content = combo_box($bool ? lang('pages', 'delete_success') : lang('pages', 'delete_error'), uri('acp/pages'));
		} else {
			redirect('errors/404');
		}
		break;
	case 'create_block':
		$form = $_POST['form'];
		$i = 0;

		if (!preg_match('/^[a-zA-Z]+\w/', $form['index_name']))
			$errors[$i++] = lang('pages', 'type_in_index_name');
		if (preg_match('/^[a-zA-Z]+\w/', $form['index_name']) && $db->select('id', 'pages_blocks', 'index_name = \'' . $db->escape($form['index_name']) . '\'', 0, 0, 0, 1) > 0)
			$errors[$i++] = lang('pages', 'index_name_unique');
		if (strlen($form['title']) < 3)
			$errors[$i++] = lang('pages', 'block_title_to_short');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$insert_values = array(
				'id' => '',
				'index_name' => $db->escape($form['index_name']),
				'title' => $db->escape($form['title']),
			);

			$bool = $db->insert('pages_blocks', $insert_values);

			$content = combo_box($bool ? lang('pages', 'create_block_success') : lang('pages', 'create_block_error'), uri('acp/pages/adm_list_blocks'));
		}
		break;
	case 'edit_block':
		$form = $_POST['form'];
		$i = 0;

		if (!preg_match('/^[a-zA-Z]+\w/', $form['index_name']))
			$errors[$i++] = lang('pages', 'type_in_index_name');
		if (preg_match('/^[a-zA-Z]+\w/', $form['index_name']) && $db->select('id', 'pages_blocks', 'index_name = \'' . $db->escape($form['index_name']) . '\' AND id != \'' . $modules->id . '\'', 0, 0, 0, 1) > 0)
			$errors[$i++] = lang('pages', 'index_name_unique');
		if (strlen($form['title']) < 3)
			$errors[$i++] = lang('pages', 'block_title_to_short');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$update_values = array(
				'index_name' => $db->escape($form['index_name']),
				'title' => $db->escape($form['title']),
			);

			$bool = $db->update('pages_blocks', $update_values, 'id = \'' . $modules->id . '\'');

			$content = combo_box($bool ? lang('pages', 'edit_block_success') : lang('pages', 'edit_block_error'), uri('acp/pages/adm_list_blocks'));
		}
		break;
	case 'delete_blocks':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && ereg('^([0-9|]+)$', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('pages', 'confirm_delete'), uri('acp/pages/adm_list_blocks/action_delete_blocks/entries_' . $marked_entries), uri('acp/pages_adm_list_blocks'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'pages_blocks', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					$bool = $db->delete('pages_blocks', 'id = \'' . $entry . '\'');
				}
			}
			$content = combo_box($bool ? lang('pages', 'delete_block_success') : lang('pages', 'delete_block_error'), uri('acp/pages/adm_list_blocks'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>