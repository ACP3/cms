<?php
namespace ACP3\Core\Validator\Rules;

use ACP3\Core;

/**
 * Class Mime
 * @package ACP3\Core\Validator\Rules
 *
 * @deprecated
 */
class Mime
{
    /**
     * @var \ACP3\Core\Validator\ValidationRules\PictureValidationRule
     */
    protected $pictureValidationRule;

    /**
     * Mime constructor.
     *
     * @param \ACP3\Core\Validator\ValidationRules\PictureValidationRule $pictureValidationRule
     */
    public function __construct(Core\Validator\ValidationRules\PictureValidationRule $pictureValidationRule)
    {
        $this->pictureValidationRule = $pictureValidationRule;
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
     *
     * @deprecated
     */
    public function isPicture($file, $width = '', $height = '', $filesize = '')
    {
        return $this->pictureValidationRule->isValid($file, [], [
            'width' => $width,
            'height' => $height,
            'filesize' => $filesize
        ]);
    }

    /**
     * Gibt in Abhängigkeit des Parameters $mimetype entweder
     * den gefundenen MIMETYPE aus oder ob der gefundene MIMETYPE
     * mit dem erwarteten übereinstimmt
     *
     * @param string $file
     *  Die zu überprüfende Datei
     * @param string $mimeType
     *  Der zu vergleichende MIMETYPE
     *
     * @return mixed
     *
     * @deprecated
     */
    public function mimeType($file, $mimeType = '')
    {
        $return = '';

        if (is_file($file) === true) {
            if (function_exists('finfo_open') === true && $fp = finfo_open(FILEINFO_MIME)) {
                $return = finfo_file($fp, $file);
                finfo_close($fp);
            } elseif (function_exists('mime_content_type') === true) {
                $return = mime_content_type($file);
            }

            if (!empty($mimeType)) {
                return $return == $mimeType ? true : false;
            }
        }

        return $return;
    }
}
