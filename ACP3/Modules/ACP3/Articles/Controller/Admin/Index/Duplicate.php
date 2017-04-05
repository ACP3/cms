<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;


use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Modules\ACP3\Articles\Model\ArticlesModel;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;

class Duplicate extends AbstractAdminAction
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;
    /**
     * @var ArticlesModel
     */
    private $articlesModel;

    /**
     * Duplicate constructor.
     * @param FrontendContext $context
     * @param ArticleRepository $articleRepository
     * @param ArticlesModel $articlesModel
     */
    public function __construct(
        FrontendContext $context,
        ArticleRepository $articleRepository,
        ArticlesModel $articlesModel
    ) {
        parent::__construct($context);

        $this->articleRepository = $articleRepository;
        $this->articlesModel = $articlesModel;
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws ResultNotExistsException
     */
    public function execute($id)
    {
        $article = $this->articleRepository->getOneById($id);

        if (!empty($article)) {
            $article['active'] = 0;
            $result = $this->articlesModel->save($article);

            return $this->redirectMessages()->setMessage(
                $result,
                $this->translator->t('system', $result !== false ? 'duplicate_success' : 'duplicate_error')
            );
        }

        throw new ResultNotExistsException();
    }
}
