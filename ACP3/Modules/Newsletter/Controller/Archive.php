<?php

namespace ACP3\Modules\Newsletter\Controller;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Class Archive
 * @package ACP3\Modules\Newsletter\Controller
 */
class Archive extends Core\Modules\Controller\Frontend
{

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Newsletter\Model $newsletterModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->newsletterModel = $newsletterModel;
    }

    public function actionDetails()
    {
        $newsletter = $this->newsletterModel->getOneById((int)$this->request->id, 1);

        if (!empty($newsletter)) {
            $this->breadcrumb
                ->append($this->lang->t('newsletter', 'index'), 'newsletter')
                ->append($this->lang->t('newsletter', 'frontend_archive_index'), 'newsletter/archive')
                ->append($newsletter['title']);

            $formatter = $this->get('core.helpers.string.formatter');

            $newsletter['date_formatted'] = $this->date->format($newsletter['date'], 'short');
            $newsletter['date_iso'] = $this->date->format($newsletter['date'], 'c');
            $newsletter['text'] = $formatter->nl2p($newsletter['text']);

            $this->view->assign('newsletter', $newsletter);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $newsletters = $this->newsletterModel->getAll(1, POS, $this->auth->entries);
        $c_newsletters = count($newsletters);

        if ($c_newsletters > 0) {
            $this->pagination->setTotalResults($this->newsletterModel->countAll(1));
            $this->pagination->display();

            for ($i = 0; $i < $c_newsletters; ++$i) {
                $newsletters[$i]['date_formatted'] = $this->date->format($newsletters[$i]['date'], 'short');
                $newsletters[$i]['date_iso'] = $this->date->format($newsletters[$i]['date'], 'c');
            }
            $this->view->assign('newsletters', $newsletters);
        }
    }

}