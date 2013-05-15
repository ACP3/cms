<?php

namespace ACP3\Installer\Modules\Errors;

/**
 * Description of Errors
 *
 * @author Tino
 */
class Errors extends \ACP3\Installer\Core\InstallerModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function action404() {
		header('HTTP/1.0 404 not found');
	}

}