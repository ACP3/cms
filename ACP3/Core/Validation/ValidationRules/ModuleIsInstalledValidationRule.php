<?php
namespace ACP3\Core\Validation\ValidationRules;
use ACP3\Core\Modules;

/**
 * Class ModuleIsInstalledValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class ModuleIsInstalledValidationRule extends AbstractValidationRule
{
    const NAME = 'module_is_installed';

    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    /**
     * ModuleIsInstalledValidationRule constructor.
     *
     * @param \ACP3\Core\Modules $modules
     */
    public function __construct(Modules $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->modules->isInstalled($data);
    }
}