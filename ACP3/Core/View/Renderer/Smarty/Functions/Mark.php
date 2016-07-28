<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class Mark
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class Mark extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Helpers\Formatter\MarkEntries
     */
    protected $markEntries;

    /**
     * Mark constructor.
     *
     * @param \ACP3\Core\Helpers\Formatter\MarkEntries $markEntries
     */
    public function __construct(Core\Helpers\Formatter\MarkEntries $markEntries)
    {
        $this->markEntries = $markEntries;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'mark';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $markAllId = !empty($params['mark_all_id']) ? $params['mark_all_id'] : 'mark-all';

        return $this->markEntries->execute($params['name'], $markAllId);
    }
}
