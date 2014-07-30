<?php

namespace ACP3\Modules\Captcha\Controller;

use ACP3\Core;

/**
 * Class Index
 * @package ACP3\Modules\Captcha\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{

    public function actionImage()
    {
        $this->setNoOutput(true);

        if (!empty($this->request->path) &&
            isset($_SESSION['captcha_' . $this->request->path])
        ) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-Type: image/gif');
            $captcha = $_SESSION['captcha_' . $this->request->path];
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