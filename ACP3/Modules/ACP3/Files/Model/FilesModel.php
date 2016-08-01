<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Model;


use ACP3\Core\Date;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var FilesRepository
     */
    protected $filesRepository;
    /**
     * @var Date
     */
    protected $date;

    /**
     * FilesModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Date $date
     * @param Secure $secure
     * @param FilesRepository $filesRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Date $date,
        Secure $secure,
        FilesRepository $filesRepository
    ) {
        parent::__construct($eventDispatcher, $filesRepository);

        $this->secure = $secure;
        $this->filesRepository = $filesRepository;
        $this->date = $date;
    }

    /**
     * @param array $formData
     * @param int $userId
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveFile(array $formData, $userId, $entryId = null)
    {
        $data = [
            'start' => $this->date->toSQL($formData['start']),
            'end' => $this->date->toSQL($formData['end']),
            'category_id' => (int)$formData['cat'],
            'title' => $this->secure->strEncode($formData['title']),
            'text' => $this->secure->strEncode($formData['text'], true),
            'comments' => (int)$formData['comments'],
            'user_id' => $userId,
        ];

        if (!empty($formData['file'])) {
            $data['file'] = $formData['file'];
        }
        if (!empty($formData['filesize'])) {
            $data['size'] = $formData['filesize'];
        }

        return $this->save($data, $entryId);
    }
}
