<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model;


use ACP3\Core\Date;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GalleryModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Secure
     */
    protected $secure;

    /**
     * GalleryModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Date $date
     * @param Secure $secure
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Date $date,
        Secure $secure,
        GalleryRepository $galleryRepository)
    {
        parent::__construct($eventDispatcher, $galleryRepository);

        $this->date = $date;
        $this->secure = $secure;
    }

    /**
     * @param array $formData
     * @param int $userId
     * @param int|null $galleryId
     * @return int|bool
     */
    public function saveGallery(array $formData, $userId, $galleryId = null)
    {
        $data = [
            'start' => $this->date->toSQL($formData['start']),
            'end' => $this->date->toSQL($formData['end']),
            'title' => $this->secure->strEncode($formData['title']),
            'user_id' => $userId,
        ];

        return $this->save($data, $galleryId);
    }
}
