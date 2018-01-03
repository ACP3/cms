<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation
     */
    protected $pollsValidator;
    /**
     * @var Polls\Model\PollsModel
     */
    protected $pollsModel;
    /**
     * @var Core\View\Block\AdminFormBlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\AdminFormBlockInterface $block
     * @param Polls\Model\PollsModel $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Validation\AdminFormValidation $pollsValidator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\AdminFormBlockInterface $block,
        Polls\Model\PollsModel $pollsModel,
        Polls\Validation\AdminFormValidation $pollsValidator
    ) {
        parent::__construct($context);

        $this->pollsModel = $pollsModel;
        $this->pollsValidator = $pollsValidator;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(function () {
            $formData = $this->request->getPost()->all();

            $this->pollsValidator->validate($formData);

            $formData['user_id'] = $this->user->getUserId();
            $pollId = $this->pollsModel->save($formData);

            $bool2 = false;
            if ($pollId !== false) {
                $bool2 = $this->pollsModel->saveAnswers($formData['answers'], $pollId);
            }

            return $pollId !== false && $bool2 !== false;
        });
    }
}
