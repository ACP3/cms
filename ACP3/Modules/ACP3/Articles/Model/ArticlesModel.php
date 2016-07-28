<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model;


use ACP3\Core\Date;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Articles\Installer\Schema;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticlesModel extends AbstractModel
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
     * ArticlesModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Date $date
     * @param Secure $secure
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Date $date,
        Secure $secure,
        ArticleRepository $articleRepository
    ) {
        parent::__construct($eventDispatcher, $articleRepository);

        $this->date = $date;
        $this->secure = $secure;
    }

    /**
     * @param array $formData
     * @param int $userId
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveArticle(array $formData, $userId, $entryId = null)
    {
        $data = [
            'start' => $this->date->toSQL($formData['start']),
            'end' => $this->date->toSQL($formData['end']),
            'title' => $this->secure->strEncode($formData['title']),
            'text' => $this->secure->strEncode($formData['text'], true),
            'user_id' => $userId,
        ];

        return $this->save($data, $entryId);
    }
}
