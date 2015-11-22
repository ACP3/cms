<?php

namespace ACP3\Modules\ACP3\Newsletter\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Archive
 * @package ACP3\Modules\ACP3\Newsletter\Controller
 */
class Archive extends Core\Modules\FrontendController
{
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository
     */
    protected $newsletterRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext            $context
     * @param Core\Pagination                                          $pagination
     * @param \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository $newsletterRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Pagination $pagination,
        Newsletter\Model\NewsletterRepository $newsletterRepository)
    {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDetails($id)
    {
        $newsletter = $this->newsletterRepository->getOneById($id, 1);

        if (!empty($newsletter)) {
            $this->breadcrumb
                ->append($this->lang->t('newsletter', 'index'), 'newsletter')
                ->append($this->lang->t('newsletter', 'frontend_archive_index'), 'newsletter/archive')
                ->append($newsletter['title']);

            return [
                'newsletter' => $newsletter
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @return array
     */
    public function actionIndex()
    {
        $this->pagination->setTotalResults($this->newsletterRepository->countAll(1));
        $this->pagination->display();

        return [
            'newsletters' => $this->newsletterRepository->getAll(1, POS, $this->user->getEntriesPerPage())
        ];
    }
}
