<?php

// gets a valid acp3_path
function get_acp3_path() {
	return dirname(__DIR__ . '/../../../../') . '/';
}

function CheckAuthentication($acp3_path) {

	static $authenticated;

	if (!isset($authenticated)) {

		$current_cwd = getcwd();

		define('IN_ACP3', true);
		if (!defined('ACP3_ROOT_DIR'))
			define('ACP3_ROOT_DIR', $acp3_path);

		require_once ACP3_ROOT_DIR . 'ACP3/Application.php';

		\ACP3\Application::defineDirConstants();
		\ACP3\Application::startupChecks();
		\ACP3\Application::includeAutoLoader();
		\ACP3\Application::initializeClasses();

		// Simulate being in the drupal root folder so we can share the session
		chdir(ACP3_ROOT_DIR);

		// if user has access permission...
		if (\ACP3\Core\Registry::get('Auth')->isUser()) {
			if (!isset($_SESSION['KCFINDER'])) {
				$_SESSION['KCFINDER'] = array();
				$_SESSION['KCFINDER']['disabled'] = false;
			}

			// User has permission, so make sure KCFinder is not disabled!
			if (!isset($_SESSION['KCFINDER']['disabled'])) {
				$_SESSION['KCFINDER']['disabled'] = false;
			}

			chdir($current_cwd);

			return true;
		}

		chdir($current_cwd);
		return false;
	}
}

CheckAuthentication(get_acp3_path());
?>