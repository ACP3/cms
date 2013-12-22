<?php
namespace ACP3\Core;

/**
 * Klasse zum beliebigen Skalieren und Ausgeben von Bildern
 *
 * @author Tino Goratsch
 */
class Image
{
    /**
     *
     * @var boolean
     */
    protected $enable_cache = false;
    /**
     *
     * @var string
     */
    protected $cache_dir = 'images/';
    /**
     *
     * @var string
     */
    protected $cache_prefix = '';
    /**
     *
     * @var integer
     */
    protected $max_width = 0;
    /**
     *
     * @var integer
     */
    protected $max_height = 0;
    /**
     *
     * @var integer
     */
    protected $jpg_quality = 85;
    /**
     *
     * @var boolean
     */
    protected $prefer_width = false;
    /**
     *
     * @var boolean
     */
    protected $prefer_height = false;
    /**
     *
     * @var string
     */
    protected $file = '';
    /**
     *
     * @var boolean
     */
    protected $force_resample = false;
    /**
     *
     * @var resource
     */
    protected $image = null;

    /**
     * Konstruktor der Klasse.
     * Überschreibt die Defaultwerte mit denen im $options-array enthaltenen Werten
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['enable_cache']) && is_bool($options['enable_cache']) === true)
            $this->enable_cache = $options['enable_cache'];
        if (isset($options['cache_prefix']))
            $this->cache_prefix = $options['cache_prefix'];
        if ($this->cache_prefix !== '' && !preg_match('/_$/', $this->cache_prefix))
            $this->cache_prefix .= '_';
        if (isset($options['max_width']) && Validate::isNumber($options['max_width']) === true)
            $this->max_width = $options['max_width'];
        if (isset($options['max_height']) && Validate::isNumber($options['max_height']) === true)
            $this->max_height = $options['max_height'];
        if (isset($options['prefer_width']) && is_bool($options['prefer_width']) === true)
            $this->prefer_width = $options['prefer_width'];
        if (isset($options['prefer_height']) && is_bool($options['prefer_height']) === true)
            $this->prefer_height = $options['prefer_height'];
        if (isset($options['jpg_quality']) && Validate::isNumber($options['jpg_quality']) === true)
            $this->jpg_quality = $options['jpg_quality'];
        if (isset($options['force_resample']) && is_bool($options['force_resample']) === true)
            $this->force_resample = $options['force_resample'];
        $this->file = $options['file'];
    }

    /**
     * Gibt den während der Bearbeitung belegten Speicher wieder frei
     */
    public function __destruct()
    {
        if (is_resource($this->image) === true)
            imagedestroy($this->image);
    }

    /**
     * Berechnet die neue Breite/Höhe eines Bildes
     *
     * @param integer $width
     *  Ausgangsbreite des Bildes
     * @param integer $height
     *  Ausgangshöhe des Bildes
     * @return array
     */
    protected function calcNewDimensions($width, $height)
    {
        if (($width >= $height || $this->prefer_width === true) && $this->prefer_height === false) {
            $newWidth = $this->max_width;
            $newHeight = intval($height * $newWidth / $width);
        } else {
            $newHeight = $this->max_height;
            $newWidth = intval($width * $newHeight / $height);
        }

        return array('width' => $newWidth, 'height' => $newHeight);
    }

    /**
     * Generiert den Namen des zu cachenden Bildes
     *
     * @return string
     */
    protected function setCacheName()
    {
        return $this->cache_prefix . substr($this->file, strrpos($this->file, '/') + 1);
    }

    /**
     * Führt die Größenanpassung des Bildes durch
     *
     * @param integer $newWidth
     * @param integer $newHeight
     * @param integer $width
     * @param integer $height
     * @param integer $type
     */
    protected function resample($newWidth, $newHeight, $width, $height, $type, $cache_file = null)
    {
        $this->image = imagecreatetruecolor($newWidth, $newHeight);
        switch ($type) {
            case 1:
                $oldPic = imagecreatefromgif($this->file);
                imagecopyresampled($this->image, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagegif($this->image, $cache_file);
                break;
            case 2:
                $oldPic = imagecreatefromjpeg($this->file);
                imagecopyresampled($this->image, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($this->image, $cache_file, $this->jpg_quality);
                break;
            case 3:
                imagealphablending($this->image, false);
                $oldPic = imagecreatefrompng($this->file);
                imagecopyresampled($this->image, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagesavealpha($this->image, true);
                imagepng($this->image, $cache_file, 9);
                break;
        }
    }

    /**
     * Gibt ein Bild direkt aus, ohne dieses in der Größe zu bearbeiten
     *
     * @return string
     */
    protected function readFromFile()
    {
        return readfile($this->file);
    }

    /**
     * Gibt das Bild aus
     */
    public function output()
    {
        if (is_file($this->file) === true) {
            $picInfo = getimagesize($this->file);
            $width = $picInfo[0];
            $height = $picInfo[1];
            $type = $picInfo[2];

            header('Cache-Control: public');
            header('Pragma: public');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($this->file)) . ' GMT');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
            header('Content-type: ' . $picInfo['mime']);

            // Falls Cache aktiviert ist und das Bild bereits gecachet wurde, dieses direkt ausgeben
            if ($this->enable_cache === true &&
                is_file(CACHE_DIR . $this->cache_dir . $this->setCacheName()) === true
            ) {
                $this->file = CACHE_DIR . $this->cache_dir . $this->setCacheName();
                $this->readFromFile();
                // Bild resampeln
            } elseif (($this->force_resample === true || ($width > $this->max_width || $height > $this->max_height)) &&
                ($type === 1 || $type === 2 || $type === 3)
            ) {
                $dimensions = $this->calcNewDimensions($width, $height);
                $cache_file = null;
                if ($this->enable_cache === true &&
                    is_file(CACHE_DIR . $this->cache_dir . $this->setCacheName()) === false
                )
                    $cache_file = CACHE_DIR . $this->cache_dir . $this->setCacheName();

                $this->resample($dimensions['width'], $dimensions['height'], $width, $height, $type, $cache_file);

                if (is_null($cache_file) === false) {
                    $this->file = $cache_file;
                    $this->readFromFile();
                }
                // Bild direkt ausgeben
            } else {
                $this->readFromFile();
            }
        }
    }
}