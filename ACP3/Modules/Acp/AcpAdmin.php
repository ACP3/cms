<?php

namespace ACP3\Modules\Acp;

use ACP3\Core;

/**
 * Description of AcpAdmin
 *
 * @author Tino
 */
class AcpAdmin extends Core\ModuleController {

	public function actionList()
	{
		$mod_list = Core\Modules::getAllModules();
		$mods = array();

		foreach ($mod_list as $name => $info) {
			$dir = strtolower($info['dir']);
			if (Core\Modules::check($dir, 'acp_list') === true && $dir !== 'acp') {
				$mods[$name]['name'] = $name;
				$mods[$name]['dir'] = $dir;
			}
		}
		Core\Registry::get('View')->assign('modules', $mods);
	}

}