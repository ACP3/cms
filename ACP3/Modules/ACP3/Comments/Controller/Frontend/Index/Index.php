<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Comments\Controller\Frontend\Index
 */
class Index extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\CommentRepository
     */
    protected $commentRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext         $context
     * @param \ACP3\Core\Pagination                                 $pagination
     * @param \ACP3\Modules\ACP3\Comments\Model\CommentRepository   $commentRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Pagination $pagination,
        Comments\Model\CommentRepository $commentRepository)
    {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param string $module
     * @param int    $entryId
     *
     * @return string
     */
    public function execute($module, $entryId)
    {
        $comments = $this->commentRepository->getAllByModule($this->modules->getModuleId($module), $entryId, POS, $this->user->getEntriesPerPage());
        $c_comments = count($comments);

        if ($c_comments > 0) {
            $this->pagination->setTotalResults($this->commentRepository->countAllByModule($this->modules->getModuleId($module), $entryId));

            for ($i = 0; $i < $c_comments; ++$i) {
                if (empty($comments[$i]['name'])) {
                    $comments[$i]['name'] = $this->translator->t('users', 'deleted_user');
                }
                if ($this->emoticonsActive === true && $this->emoticonsHelpers) {
                    $comments[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($comments[$i]['message']);
                }
            }

            $this->view->assign('comments', $comments);
            $this->view->assign('dateformat', $this->commentsSettings['dateformat']);
            $this->view->assign('pagination', $this->pagination->render());
        }

        return $this->view->fetchTemplate('Comments/Frontend/index.index.tpl');
    }
}
