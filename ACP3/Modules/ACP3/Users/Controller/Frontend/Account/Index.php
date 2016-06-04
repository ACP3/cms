<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Account
 */
class Index extends AbstractAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Users\Model\UserRepository $userRepository
    ) {
        parent::__construct($context);

        $this->userRepository = $userRepository;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost();
        }

        $user = $this->userRepository->getOneById($this->user->getUserId());

        return [
            'draft' => $user['draft']
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost()
    {
        $updateValues = [
            'draft' => $this->get('core.helpers.secure')->strEncode($this->request->getPost()->get('draft', ''), true)
        ];
        $bool = $this->userRepository->update($updateValues, $this->user->getUserId());

        return $this->redirectMessages()->setMessage(
            $bool,
            $this->translator->t('system', $bool !== false ? 'edit_success' : 'edit_error')
        );
    }
}
