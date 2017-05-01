<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\View\Block\Frontend;


use ACP3\Core\View\Block\AbstractFormBlock;

class PollVoteFormBlock extends AbstractFormBlock
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();
        $poll = $data['poll'];

        return [
            'question' => $poll['title'],
            'multiple' => $poll['multiple'],
            'answers' => $data['answers'],
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
