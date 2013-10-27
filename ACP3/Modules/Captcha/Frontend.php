<?php

/**
 * Captcha
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Captcha;

use ACP3\Core;

/**
 * Description of Frontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller {

	public function __construct() {
		parent::__construct();
	}

	public function actionImage()
	{
		$this->view->setNoOutput(true);

		if (!empty($this->uri->path) &&
			isset($_SESSION['captcha_' . $this->uri->path])) {
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-Type: image/gif');
			$captcha = $_SESSION['captcha_' . $this->uri->path];
			$captchaLength = strlen($captcha);
			$width = $captchaLength * 25;
			$height = 30;

			$im = imagecreate($width, $height);
			// Hintergrundfarbe
			imagecolorallocate($im, 255, 255, 255);

			$textColor = imagecolorallocate($im, 0, 0, 0);

			for ($i = 0; $i < $captchaLength; ++$i) {
				$font = mt_rand(2, 5);
				$posLeft = 22 * $i + 10;
				$posTop = mt_rand(1, $height - imagefontheight($font) - 3);
				imagestring($im, $font, $posLeft, $posTop, $captcha[$i], $textColor);
			}
			imagegif($im);
			imagedestroy($im);
		}
	}
}