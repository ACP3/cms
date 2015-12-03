<?php
namespace ACP3\Modules\ACP3\Categories\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\CategoryRepository;
use ACP3\Modules\ACP3\Categories\Validation\ValidationRules\DuplicateCategoryValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Categories\Validation
 */
class Validator extends Core\Validation\AbstractValidator
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                                        $lang
     * @param \ACP3\Core\Validation\Validator                        $validator
     * @param \ACP3\Modules\ACP3\Categories\Model\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validation\Validator $validator,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($lang, $validator);

        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array      $formData
     * @param null|array $file
     * @param array      $settings
     * @param int|string $categoryId
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $file, array $settings, $categoryId = '')
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('categories', 'title_to_short')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'description',
                    'message' => $this->lang->t('categories', 'description_to_short')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\PictureValidationRule::NAME,
                [
                    'data' => $file,
                    'field' => 'picture',
                    'message' => $this->lang->t('categories', 'invalid_image_selected'),
                    'extra' => [
                        'width' => $settings['width'],
                        'height' => $settings['height'],
                        'filesize' => $settings['filesize'],
                        'required' => false
                    ]
                ])
            ->addConstraint(
                DuplicateCategoryValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('categories', 'category_already_exists'),
                    'extra' => [
                        'module_id' => empty($categoryId) ? $formData['module'] : $this->categoryRepository->getModuleIdByCategoryId($categoryId),
                        'category_id' => $categoryId
                    ]
                ]);

        if (empty($categoryId)) {
            $this->validator->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'module',
                    'message' => $this->lang->t('categories', 'select_module')
                ]);
        }

        $this->validator->validate();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'width',
                    'message' => $this->lang->t('categories', 'invalid_image_width_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'height',
                    'message' => $this->lang->t('categories', 'invalid_image_height_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'filesize',
                    'message' => $this->lang->t('categories', 'invalid_image_filesize_entered')
                ]);

        $this->validator->validate();
    }
}
