<?php
function smarty_function_icon($params)
{
	$path = DESIGN_PATH . 'images/' . $params['path'] . '.png';
	$width = $height = '';

	if (!empty($params['width']) && !empty($params['height']) &&
		validate::isNumber($params['width']) && validate::isNumber($params['height'])) {
		$width = ' width="' . $params['width'] . '"';
		$height = ' height="' . $params['height'] . '"';
	} elseif (is_file(ACP3_ROOT . $path)) {
		$picInfos = getimagesize(ACP3_ROOT . $path);
		$width = ' width="' . $picInfos[0] . '"';
		$height = ' height="' . $picInfos[1] . '"';
	}

	$alt = ' alt="' . (!empty($params['alt']) ? $params['alt'] : '') . '"';
	$title = !empty($params['title']) ? ' title="' . $params['title'] . '"' : '';

	return '<img src="' . $path . '"' . $width . $height . $alt . $title . ' />';
}
/* vim: set expandtab: */
?>