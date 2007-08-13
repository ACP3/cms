<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check())
	redirect('errors/403');
$pic = isset($modules->gen['pic']) && ereg('[0-9]', $modules->gen['pic']) ? $modules->gen['pic'] : 1;

if (isset($_POST['submit'])) {
	include 'modules/gallery/entry.php';
}
if (!isset($_POST['submit']) || isset($error_msg)) {
	$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

	$galleries = $db->select('id, start, name', 'gallery', 0, 'start DESC');
	$c_galleries = count($galleries);

	for ($i = 0; $i < $c_galleries; $i++) {
		$galleries[$i]['selected'] = select_entry('gallery', $galleries[$i]['id'], $modules->id);
		$galleries[$i]['date'] = date_aligned(1, $galleries[$i]['start']);
		$galleries[$i]['name'] = $galleries[$i]['name'];
	}

	$tpl->assign('galleries', $galleries);
	$form['pic'] = isset($form['pic']) ? $form['pic'] : $pic;
	$tpl->assign('form', $form);

	$content = $tpl->fetch('gallery/add_picture.html');
}
?>