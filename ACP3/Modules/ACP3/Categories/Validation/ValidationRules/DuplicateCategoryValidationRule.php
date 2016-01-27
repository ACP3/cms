<?php
namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Helpers;

/**
 * Class DuplicateCategoryValidationRule
 * @package ACP3\Modules\ACP3\Categories\Validation\ValidationRules
 */
class DuplicateCategoryValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelper;

    /**
     * DuplicateCategoryValidationRule constructor.
     *
     * @param \ACP3\Modules\ACP3\Categories\Helpers $categoriesHelper
     */
    public function __construct(Helpers $categoriesHelper)
    {
        $this->categoriesHelper = $categoriesHelper;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        $params = array_merge([
            'module_id' => 0,
            'category_id' => ''
        ], $extra);

        return $this->categoriesHelper->categoryIsDuplicate(
            $data,
            $params['module_id'],
            $params['category_id']
        );
    }
}