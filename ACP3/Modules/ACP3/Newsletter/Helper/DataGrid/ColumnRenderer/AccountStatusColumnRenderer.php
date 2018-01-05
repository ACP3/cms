<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Helper\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Router\RouterInterface;

class AccountStatusColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;

    /**
     * AccountStatusColumnRenderer constructor.
     *
     * @param \ACP3\Core\I18n\TranslatorInterface $translator
     * @param \ACP3\Core\Router\RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        if (isset($dbResultRow[$field])) {
            if ((int)$dbResultRow[$field] === 0) {
                $route = $this->router->route('acp/newsletter/accounts/activate/id_' . $dbResultRow[$this->primaryKey]);
                $title = $this->translator->t('newsletter', 'activate_account');
                $value = '<a href="' . $route . '" title="' . $title . '">';
                $value .= '<i class="fa fa-times text-danger"></i>';
                $value .= '</a>';
            } else {
                $value = '<i class="fa fa-check text-success"></i>';
            }

            return $value;
        }

        return null;
    }
}
