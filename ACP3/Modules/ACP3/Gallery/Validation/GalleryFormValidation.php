<?php
namespace ACP3\Modules\ACP3\Gallery\Validation;

use ACP3\Core;

/**
 * Class GalleryFormValidation
 * @package ACP3\Modules\ACP3\Gallery\Validation
 */
class GalleryFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    protected $uriAlias = '';

    /**
     * @param string $uriAlias
     *
     * @return $this
     */
    public function setUriAlias($uriAlias)
    {
        $this->uriAlias = $uriAlias;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('gallery', 'type_in_gallery_title')
                ]);

        $this->validator->dispatchValidationEvent(
            'seo.validation.validate_uri_alias',
            $formData,
            ['path' => $this->uriAlias]
        );

        $this->validator->validate();
    }
}
