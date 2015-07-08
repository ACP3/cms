<?php

namespace ACP3\Modules\ACP3\Captcha\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Captcha\Controller
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\SessionHandler                     $sessionHandler
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\SessionHandler $sessionHandler
    )
    {
        parent::__construct($context);

        $this->sessionHandler = $sessionHandler;
    }

    public function actionImage()
    {
        $this->setNoOutput(true);

        if ($this->request->getParameters()->has('path') &&
            $this->sessionHandler->has('captcha_' . $this->request->getParameters()->get('path'))
        ) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-Type: image/gif');
            $captcha = $this->sessionHandler->get('captcha_' . $this->request->getParameters()->get('path'));
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
