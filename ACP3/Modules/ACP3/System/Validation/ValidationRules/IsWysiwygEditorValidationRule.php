<?php
namespace ACP3\Modules\ACP3\System\Validation\ValidationRules;


use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\WYSIWYG\AbstractWYSIWYG;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class IsWysiwygEditorValidationRule
 * @package ACP3\Modules\ACP3\System\Validation\ValidationRules
 */
class IsWysiwygEditorValidationRule extends AbstractValidationRule
{
    const NAME = 'system_is_wysiwyg_editor';

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * IsWysiwygEditorValidationRule constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->isValidWysiwygEditor($data);
    }

    /**
     * @param string $data
     *
     * @return bool
     */
    protected function isValidWysiwygEditor($data)
    {
        return !empty($data) && $this->container->has($data) && $this->container->get($data) instanceof AbstractWYSIWYG;
    }
}