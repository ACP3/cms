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
     * @var Core\Lang
     */
    protected $lang;

    /**
     * @param Core\Lang $lang
     */
    public function __construct(Core\Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'mark';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $markAllId = !empty($params['mark_all_id']) ? $params['mark_all_id'] : 'mark-all';
        $deleteOptions = json_encode(
            [
                'checkBoxName' => $params['name'],
                'language' => [
                    'confirmationTextSingle' => $this->lang->t('system', 'confirm_delete_single'),
                    'confirmationTextMultiple' => $this->lang->t('system', 'confirm_delete_multiple'),
                    'noEntriesSelectedText' => $this->lang->t('system', 'no_entries_selected')
                ]
            ]
        );

        return 'data-mark-all-id="' . $markAllId . '" data-checkbox-name="' . $params['name'] . '" data-delete-options=\'' . $deleteOptions . '\'';
    }
}
