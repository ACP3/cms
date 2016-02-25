<?php
namespace ACP3\Modules\ACP3\Newsletter\Helper\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router;
use ACP3\Core\RouterInterface;

/**
 * Class AccountStatusColumnRenderer
 * @package ACP3\Modules\ACP3\Newsletter\Helper\DataGrid\ColumnRenderer
 */
class AccountStatusColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;

    /**
     * AccountStatusColumnRenderer constructor.
     *
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\RouterInterface $router
     */
    public function __construct(
        Translator $translator,
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
                $value .= '<i class="glyphicon glyphicon-remove text-danger"></i>';
                $value .= '</a>';
            } else {
                $value = '<i class="glyphicon glyphicon-ok text-success"></i>';
            }

            return $value;
        }

        return null;
    }
}
