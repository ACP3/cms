<?php
namespace ACP3\Modules\ACP3\Categories\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Categories\Helpers;

/**
 * Class CategoryExistsValidationRule
 * @package ACP3\Modules\ACP3\Categories\Validation\ValidationRules
 */
class CategoryExistsValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Helpers
     */
    protected $categoriesHelper;

    /**
     * CategoryExistsValidationRule constructor.
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
        if (is_array($data) && is_array($field)) {
            $categoryId = reset($field);
            $createCategory = next($field);

            return !empty($data[$createCategory]) || $this->categoriesHelper->categoryExists($data[$categoryId]);
        }

        return false;
    }
}