<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;
use ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EmoticonsModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Secure
     */
    protected $secure;

    /**
     * EmoticonsModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Secure $secure
     * @param EmoticonRepository $emoticonRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Secure $secure,
        EmoticonRepository $emoticonRepository
    ) {
        parent::__construct($eventDispatcher, $emoticonRepository);

        $this->secure = $secure;
    }

    /**
     * @param array $formData
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveEmoticon(array $formData, $entryId = null)
    {
        $data = [
            'code' => $this->secure->strEncode($formData['code']),
            'description' => $this->secure->strEncode($formData['description']),
            'img' => $formData['img'],
        ];

        return $this->save($data, $entryId);
    }
}
