<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Gallery;

class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * AbstractFormAction constructor.
     */
    public function __construct(FrontendContext $context, Forms $formsHelper)
    {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
    }

    /**
     * @param string $currentValue
     *
     * @return array
     */
    protected function getOptions($currentValue = '0')
    {
        $comments = [
            '1' => $this->translator->t('system', 'allow_comments'),
        ];

        return $this->formsHelper->checkboxGenerator('comments', $comments, $currentValue);
    }

    protected function canUseComments()
    {
        $settings = $this->config->getSettings(Gallery\Installer\Schema::MODULE_NAME);

        return $settings['overlay'] == 0 && $settings['comments'] == 1 && $this->modules->isActive('comments');
    }
}
