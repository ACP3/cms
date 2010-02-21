<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit();

/**
 * Baut den String des zu erstellenden /
 * verändernden Zugriffslevels zusammen
 *
 * @param array $modules
 * @return string
 */
function buildAccessLevel($modules)
{
	if (!empty($modules) && is_array($modules)) {
		$modules['errors'] = array('read' => 1, 'create' => 2, 'edit' => 4, 'delete' => 8, 'full' => 16);
		ksort($modules);
		$access_level = '';

		foreach ($modules as $mod => $levels) {
			if (isset($levels['full'])) {
				$level = 16;
			} else {
				$level = 0;
				$level+= isset($levels['read']) ? 1 : 0;
				$level+= isset($levels['create']) ? 2 : 0;
				$level+= isset($levels['edit']) ? 4 : 0;
				$level+= isset($levels['delete']) ? 8 : 0;
			}
			$access_level.= $mod . ':' . $level . ',';
		}
		return substr($access_level, 0, -1);
	}
	return '';
}
