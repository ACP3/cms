<?php
namespace ACP3\Core\Validator\ValidationRules;

/**
 * Class IsPictureValidationRule
 * @package ACP3\Core\Validator\ValidationRules
 */
class PictureValidationRule extends AbstractValidationRule
{
    const NAME = 'picture';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        $params = array_merge([
            'width' => 0,
            'height' => 0,
            'filesize' => 0,
            'required' => true
        ], $extra);

        if ($this->isFileUpload($data)) {
            return $this->isPicture(
                $data['tmp_name'],
                $params['width'],
                $params['height'],
                $params['filesize']
            );
        } elseif ($params['required'] === false && empty($data)) {
            return true;
        }

        return false;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    protected function isFileUpload($data)
    {
        return (is_array($data) && !empty($data['tmp_name']) && !empty($data['size']) && $data['error'] !== UPLOAD_ERR_OK);
    }

    /**
     * @param string $file
     * @param int    $width
     * @param int    $height
     * @param int    $filesize
     *
     * @return bool
     */
    protected function isPicture($file, $width = 0, $height = 0, $filesize = 0)
    {
        $info = getimagesize($file);
        $isPicture = ($info[2] >= 1 && $info[2] <= 3);

        if ($isPicture === true) {
            $bool = true;
            // Optional parameters
            if ($this->validateOptionalParameters($file, $info, $width, $height, $filesize)) {
                $bool = false;
            }

            return $bool;
        }
        return false;
    }

    /**
     * @param string $file
     * @param array  $info
     *
     * @param int    $width
     * @param int    $height
     * @param int    $filesize
     *
     * @return bool
     */
    protected function validateOptionalParameters($file, array $info, $width, $height, $filesize)
    {
        return $width > 0 && $info[0] > $width ||
        $height > 0 && $info[1] > $height ||
        $filesize > 0 && filesize($file) > $filesize;
    }

}