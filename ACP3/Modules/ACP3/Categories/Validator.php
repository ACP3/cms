<?php
namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\CategoryRepository;
use ACP3\Modules\ACP3\Categories\Validator\ValidationRules\DuplicateCategoryValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Categories
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var \ACP3\Core\Validator\Validator
     */
    protected $validator;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                                        $lang
     * @param \ACP3\Core\Validator\Validator                         $validator
     * @param \ACP3\Core\Validator\Rules\Misc                        $validate
     * @param \ACP3\Modules\ACP3\Categories\Model\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($lang, $validate);

        $this->validator = $validator;
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
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('categories', 'title_to_short')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'description',
                    'message' => $this->lang->t('categories', 'description_to_short')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\PictureValidationRule::NAME,
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
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
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
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'width',
                    'message' => $this->lang->t('categories', 'invalid_image_width_entered')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'height',
                    'message' => $this->lang->t('categories', 'invalid_image_height_entered')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'filesize',
                    'message' => $this->lang->t('categories', 'invalid_image_filesize_entered')
                ]);

        $this->validator->validate();
    }
}
