<?php
namespace ACP3\Modules\Gallery;

use ACP3\Core;

class Cache
{
    /**
     * @var string
     */
    const CACHE_ID = 'pics_id_';
    /**
     * @var \ACP3\Core\Cache2
     */
    protected $cache;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Model
     */
    protected $galleryModel;

    public function __construct(\Doctrine\DBAL\Connection $db, Model $galleryModel)
    {
        $this->cache = new Core\Cache2('gallery');
        $this->db = $db;
        $this->galleryModel = $galleryModel;
    }

    /**
     * Erstellt den Galerie-Cache anhand der angegebenen ID
     *
     * @param integer $id
     *  Die ID der zu cachenden Galerie
     * @return boolean
     */
    public function setCache($id)
    {
        $pictures = $this->galleryModel->getPicturesByGalleryId($id);
        $c_pictures = count($pictures);

        $cache = new Core\Config($this->db, 'gallery');
        $settings = $cache->getSettings();

        for ($i = 0; $i < $c_pictures; ++$i) {
            $pictures[$i]['width'] = $settings['thumbwidth'];
            $pictures[$i]['height'] = $settings['thumbheight'];
            $picInfos = @getimagesize(UPLOADS_DIR . 'gallery/' . $pictures[$i]['file']);
            if ($picInfos !== false) {
                if ($picInfos[0] > $settings['thumbwidth'] || $picInfos[1] > $settings['thumbheight']) {
                    $newHeight = $settings['thumbheight'];
                    $newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
                }

                $pictures[$i]['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
                $pictures[$i]['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
            }
        }

        return $this->cache->save(self::CACHE_ID . $id, $pictures);
    }

    /**
     * Bindet die gecachete Galerie anhand ihrer ID ein
     *
     * @param integer $id
     *  Die ID der Galerie
     * @return array
     */
    public function getCache($id)
    {
        if ($this->cache->contains(self::CACHE_ID . $id) === false) {
            $this->setCache($id);
        }

        return $this->cache->fetch(self::CACHE_ID . $id);
    }
} 