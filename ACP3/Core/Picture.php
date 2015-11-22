<?php
namespace ACP3\Core;

use Symfony\Component\HttpFoundation\Response;

/**
 * @package ACP3\Core
 */
class Picture
{
    /**
     * @var boolean
     */
    protected $enableCache = false;
    /**
     * @var string
     */
    protected $cacheDir = 'images/';
    /**
     * @var string
     */
    protected $cachePrefix = '';
    /**
     * @var integer
     */
    protected $maxWidth = 0;
    /**
     * @var integer
     */
    protected $maxHeight = 0;
    /**
     * @var integer
     */
    protected $jpgQuality = 85;
    /**
     * @var boolean
     */
    protected $preferWidth = false;
    /**
     * @var boolean
     */
    protected $preferHeight = false;
    /**
     * @var string
     */
    protected $file = '';
    /**
     * @var boolean
     */
    protected $forceResample = false;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;
    /**
     * @var resource
     */
    protected $image;
    /**
     * @var string
     */
    protected $environment = '';

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string                                     $environment
     */
    public function __construct(
        Response $response,
        $environment
    )
    {
        $this->response = $response;
        $this->environment = $environment;
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
     * @param boolean $enableCache
     *
     * @return $this
     */
    public function setEnableCache($enableCache)
    {
        $this->enableCache = (bool)$enableCache;
        return $this;
    }

    /**
     * @param string $cacheDir
     *
     * @return $this
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        return $this;
    }

    /**
     * @param string $cachePrefix
     *
     * @return $this
     */
    public function setCachePrefix($cachePrefix)
    {
        $this->cachePrefix = $cachePrefix;
        return $this;
    }

    /**
     * @param int $maxWidth
     *
     * @return $this
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = (int)$maxWidth;
        return $this;
    }

    /**
     * @param int $maxHeight
     *
     * @return $this
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = (int)$maxHeight;
        return $this;
    }

    /**
     * @param int $jpgQuality
     *
     * @return $this
     */
    public function setJpgQuality($jpgQuality)
    {
        $this->jpgQuality = (int)$jpgQuality;
        return $this;
    }

    /**
     * @param boolean $preferWidth
     *
     * @return $this
     */
    public function setPreferWidth($preferWidth)
    {
        $this->preferWidth = (bool)$preferWidth;
        return $this;
    }

    /**
     * @param boolean $preferHeight
     *
     * @return $this
     */
    public function setPreferHeight($preferHeight)
    {
        $this->preferHeight = (bool)$preferHeight;
        return $this;
    }

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @param boolean $forceResample
     *
     * @return $this
     */
    public function setForceResample($forceResample)
    {
        $this->forceResample = (bool)$forceResample;
        return $this;
    }

    /**
     * @return bool
     */
    public function process()
    {
        if (is_file($this->file) === true) {
            $cacheFile = $this->getCacheFileName();
            $picInfo = getimagesize($this->file);
            $width = $picInfo[0];
            $height = $picInfo[1];
            $type = $picInfo[2];

            $this->setHeaders($picInfo['mime']);

            // Direct output of the picture, if it is already cached
            if ($this->enableCache === true && is_file($cacheFile) === true) {
                $this->file = $cacheFile;
            } elseif ($this->resamplingIsNecessary($width, $height, $type)) { // Resize the picture
                $dimensions = $this->calcNewDimensions($width, $height);

                $this->createCacheDir();

                $this->resample(
                    $dimensions['width'],
                    $dimensions['height'],
                    $width,
                    $height,
                    $type,
                    $cacheFile
                );
                $this->file = $cacheFile;
            }

            return true;
        } else {
            $this->setHeaders('image/jpeg');
        }

        return false;
    }

    /**
     * @return string
     */
    public function getWebFilePath()
    {
        return ROOT_DIR . 'cache/' . $this->environment . '/' . $this->cacheDir . $this->getCacheName();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendResponse()
    {
        return $this->response->setContent($this->readFromFile());
    }

    /**
     * Get the name of a possibly cached picture
     *
     * @return string
     */
    protected function getCacheFileName()
    {
        return CACHE_DIR . $this->cacheDir . $this->getCacheName();
    }

    /**
     * Generiert den Namen des zu cachenden Bildes
     *
     * @return string
     */
    protected function getCacheName()
    {
        return $this->cachePrefix . substr($this->file, strrpos($this->file, '/') + 1);
    }

    /**
     * Reads the contents of the requested picture
     *
     * @return string
     */
    protected function readFromFile()
    {
        return file_get_contents($this->file);
    }

    /**
     * Berechnet die neue Breite/Höhe eines Bildes
     *
     * @param integer $width
     *  Ausgangsbreite des Bildes
     * @param integer $height
     *  Ausgangshöhe des Bildes
     *
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

        return ['width' => $newWidth, 'height' => $newHeight];
    }

    /**
     * Resamples the picture to the given values
     *
     * @param integer $newWidth
     * @param integer $newHeight
     * @param integer $width
     * @param integer $height
     * @param integer $type
     * @param string  $cacheFile
     */
    protected function resample($newWidth, $newHeight, $width, $height, $type, $cacheFile)
    {
        $this->image = imagecreatetruecolor($newWidth, $newHeight);
        switch ($type) {
            case 1:
                $origPicture = imagecreatefromgif($this->file);
                $this->scalePicture($newWidth, $newHeight, $width, $height, $origPicture);
                imagegif($this->image, $cacheFile);
                break;
            case 2:
                $origPicture = imagecreatefromjpeg($this->file);
                $this->scalePicture($newWidth, $newHeight, $width, $height, $origPicture);
                imagejpeg($this->image, $cacheFile, $this->jpgQuality);
                break;
            case 3:
                imagealphablending($this->image, false);
                $origPicture = imagecreatefrompng($this->file);
                $this->scalePicture($newWidth, $newHeight, $width, $height, $origPicture);
                imagesavealpha($this->image, true);
                imagepng($this->image, $cacheFile, 9);
                break;
        }

        imagedestroy($this->image);
    }

    /**
     * @param string $mimeType
     */
    protected function setHeaders($mimeType)
    {
        $this->response->headers->add([
            'Content-type' => $mimeType,
            'Cache-Control' => 'public',
            'Pragma' => 'public',
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($this->file)) . ' GMT',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT'
        ]);
    }

    /**
     * @param int      $newWidth
     * @param int      $newHeight
     * @param int      $width
     * @param int      $height
     * @param resource $origPicture
     */
    protected function scalePicture($newWidth, $newHeight, $width, $height, $origPicture)
    {
        imagecopyresampled($this->image, $origPicture, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $type
     *
     * @return bool
     */
    protected function resamplingIsNecessary($width, $height, $type)
    {
        return ($this->forceResample === true || ($width > $this->maxWidth || $height > $this->maxHeight)) && ($type === 1 || $type === 2 || $type === 3);
    }

    /**
     * Creates the cache directory if it's not already present
     */
    protected function createCacheDir()
    {
        $path = CACHE_DIR . $this->cacheDir;
        if (!is_dir($path) && is_writable(CACHE_DIR)) {
            mkdir($path);
        }
    }
}
