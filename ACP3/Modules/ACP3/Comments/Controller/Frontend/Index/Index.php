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
    use Core\Cache\CacheResponseTrait;
    
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\CommentRepository
     */
    protected $commentRepository;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Pagination $pagination
     * @param \ACP3\Modules\ACP3\Comments\Model\CommentRepository $commentRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Pagination $pagination,
        Comments\Model\CommentRepository $commentRepository
    ) {
        parent::__construct($context);

        $this->pagination = $pagination;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param string $module
     * @param int $entryId
     *
     * @return array
     */
    public function execute($module, $entryId)
    {
        $this->setCacheResponseCacheable($this->config->getSettings('system')['cache_lifetime']);

        $comments = $this->commentRepository->getAllByModule(
            $this->modules->getModuleId($module),
            $entryId,
            POS,
            $this->user->getEntriesPerPage()
        );
        $cComments = count($comments);

        if ($cComments > 0) {
            $this->pagination->setTotalResults(
                $this->commentRepository->countAllByModule($this->modules->getModuleId($module), $entryId)
            );

            for ($i = 0; $i < $cComments; ++$i) {
                if (empty($comments[$i]['name'])) {
                    $comments[$i]['name'] = $this->translator->t('users', 'deleted_user');
                }
                if ($this->emoticonsActive === true && $this->emoticonsHelpers) {
                    $comments[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($comments[$i]['message']);
                }
            }

            return [
                'comments' => $comments,
                'dateformat' => $this->commentsSettings['dateformat'],
                'pagination' => $this->pagination->render()
            ];
        }

        return [];
    }
}
