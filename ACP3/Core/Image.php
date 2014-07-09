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
    protected $enableCache = false;
    /**
     *
     * @var string
     */
    protected $cacheDir = 'images/';
    /**
     *
     * @var string
     */
    protected $cachePrefix = '';
    /**
     *
     * @var integer
     */
    protected $maxWidth = 0;
    /**
     *
     * @var integer
     */
    protected $maxHeight = 0;
    /**
     *
     * @var integer
     */
    protected $jpgQuality = 85;
    /**
     *
     * @var boolean
     */
    protected $preferWidth = false;
    /**
     *
     * @var boolean
     */
    protected $preferHeight = false;
    /**
     *
     * @var string
     */
    protected $file = '';
    /**
     *
     * @var boolean
     */
    protected $forceResample = false;
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
        if (isset($options['enable_cache']) && is_bool($options['enable_cache']) === true) {
            $this->enableCache = $options['enable_cache'];
        }
        if (isset($options['cache_prefix'])) {
            $this->cachePrefix = $options['cache_prefix'];
        }
        if ($this->cachePrefix !== '' && !preg_match('/_$/', $this->cachePrefix)) {
            $this->cachePrefix .= '_';
        }
        if (isset($options['max_width'])) {
            $this->maxWidth = (int) $options['max_width'];
        }
        if (isset($options['max_height'])) {
            $this->maxHeight = (int) $options['max_height'];
        }
        if (isset($options['prefer_width']) && is_bool($options['prefer_width']) === true) {
            $this->preferWidth = $options['prefer_width'];
        }
        if (isset($options['prefer_height']) && is_bool($options['prefer_height']) === true) {
            $this->preferHeight = $options['prefer_height'];
        }
        if (isset($options['jpg_quality'])) {
            $this->jpgQuality = (int) $options['jpg_quality'];
        }
        if (isset($options['force_resample']) && is_bool($options['force_resample']) === true) {
            $this->forceResample = $options['force_resample'];
        }

        $this->file = $options['file'];
    }

    /**
     * Gibt den während der Bearbeitung belegten Speicher wieder frei
     */
    public function __destruct()
    {
        if (is_resource($this->image) === true) {
            imagedestroy($this->image);
        }
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
        if (($width >= $height || $this->preferWidth === true) && $this->preferHeight === false) {
            $newWidth = $this->maxWidth;
            $newHeight = intval($height * $newWidth / $width);
        } else {
            $newHeight = $this->maxHeight;
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
        return $this->cachePrefix . substr($this->file, strrpos($this->file, '/') + 1);
    }

    /**
     * Führt die Größenanpassung des Bildes durch
     *
     * @param integer $newWidth
     * @param integer $newHeight
     * @param integer $width
     * @param integer $height
     * @param integer $type
     * @param null $cacheFile
     */
    protected function resample($newWidth, $newHeight, $width, $height, $type, $cacheFile = null)
    {
        $this->image = imagecreatetruecolor($newWidth, $newHeight);
        switch ($type) {
            case 1:
                $oldPic = imagecreatefromgif($this->file);
                imagecopyresampled($this->image, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagegif($this->image, $cacheFile);
                break;
            case 2:
                $oldPic = imagecreatefromjpeg($this->file);
                imagecopyresampled($this->image, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($this->image, $cacheFile, $this->jpgQuality);
                break;
            case 3:
                imagealphablending($this->image, false);
                $oldPic = imagecreatefrompng($this->file);
                imagecopyresampled($this->image, $oldPic, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagesavealpha($this->image, true);
                imagepng($this->image, $cacheFile, 9);
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
     * Get the name of a possibly cached picture
     *
     * @return string
     */
    protected function getCacheFileName()
    {
        return CACHE_DIR . $this->cacheDir . $this->setCacheName();
    }

    /**
     * Gibt das Bild aus
     */
    public function output()
    {
        if (is_file($this->file) === true) {
            $cacheFile = $this->getCacheFileName();
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
            if ($this->enableCache === true && file_exists($cacheFile) === true) {
                $this->file = $cacheFile;
                $this->readFromFile();
            } elseif (($this->forceResample === true || ($width > $this->maxWidth || $height > $this->maxHeight)) && ($type === 1 || $type === 2 || $type === 3)) { // Bild resampeln
                $dimensions = $this->calcNewDimensions($width, $height);

                $this->resample($dimensions['width'], $dimensions['height'], $width, $height, $type, $cacheFile);
                $this->file = $cacheFile;
                $this->readFromFile();
            } else {
                $this->readFromFile(); // Bild direkt ausgeben
            }
        }
    }
}