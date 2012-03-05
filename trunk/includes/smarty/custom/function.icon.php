<?php
function smarty_function_icon($params)
{
	$path = DESIGN_PATH . 'images/' . $params['path'] . '.png';
	$width = $height = '';

	if (!empty($params['width']) && !empty($params['height']) &&
		ACP3_Validate::isNumber($params['width']) === true && ACP3_Validate::isNumber($params['height']) === true) {
		$width = ' width="' . $params['width'] . '"';
		$height = ' height="' . $params['height'] . '"';
	} elseif (is_file(ACP3_ROOT . $path) === true) {
		$picInfos = getimagesize(ACP3_ROOT . $path);
		$width = ' width="' . $picInfos[0] . '"';
		$height = ' height="' . $picInfos[1] . '"';
	}

	$alt = ' alt="' . (!empty($params['alt']) ? $params['alt'] : '') . '"';
	$title = !empty($params['title']) ? ' title="' . $params['title'] . '"' : '';

	return '<img src="' . $path . '"' . $width . $height . $alt . $title . ' />';
}
/* vim: set expandtab: */