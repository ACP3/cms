<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model;


use ACP3\Core\Date;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticlesModel extends AbstractModel
{
    /**
     * @var ArticleRepository
     */
    protected $articlesRepository;
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Secure
     */
    protected $secure;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Date $date,
        Secure $secure,
        ArticleRepository $articleRepository
    ) {
        parent::__construct($eventDispatcher);

        $this->date = $date;
        $this->secure = $secure;
        $this->articlesRepository = $articleRepository;
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

        return $this->save($this->articlesRepository, $data, $entryId);
    }
}
