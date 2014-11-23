<?php
namespace ACP3\Core\Validator\Rules;

use ACP3\Core;

/**
 * Class Mime
 * @package ACP3\Core\Validator\Rules
 */
class Mime
{
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;

    /**
     * @param Misc $validate
     */
    public function __construct(Core\Validator\Rules\Misc $validate)
    {
        $this->validate = $validate;
    }

    /**
     * Überprüfen, ob es ein unterstütztes Bildformat ist
     *
     * @param string $file
     *  Zu überprüfendes Bild
     * @param string $width
     * @param string $height
     * @param string $filesize
     *
     * @return boolean
     */
    public function isPicture($file, $width = '', $height = '', $filesize = '')
    {
        $info = getimagesize($file);
        $isPicture = $info[2] >= 1 && $info[2] <= 3 ? true : false;

        if ($isPicture === true) {
            $bool = true;
            // Optionale Parameter
            if ($this->validate->isNumber($width) && $info[0] > $width ||
                $this->validate->isNumber($height) && $info[1] > $height ||
                filesize($file) === 0 || $this->validate->isNumber($filesize) && filesize($file) > $filesize
            ) {
                $bool = false;
            }

            return $bool;
        }
        return false;
    }

    /**
     * Gibt in Abhängigkeit des Parameters $mimetype entweder
     * den gefundenen MIMETYPE aus oder ob der gefundene MIMETYPE
     * mit dem erwarteten übereinstimmt
     *
     * @param string $file
     *  Die zu überprüfende Datei
     * @param string $mimetype
     *  Der zu vergleichende MIMETYPE
     *
     * @return mixed
     */
    public function mimeType($file, $mimetype = '')
    {
        $return = '';

        if (is_file($file) === true) {
            if (function_exists('finfo_open') === true && $fp = finfo_open(FILEINFO_MIME)) {
                $return = finfo_file($fp, $file);
                finfo_close($fp);
            } elseif (function_exists('mime_content_type') === true) {
                $return = mime_content_type($file);
            }

            if (!empty($mimetype)) {
                return $return == $mimetype ? true : false;
            }
        }

        return $return;
    }

}