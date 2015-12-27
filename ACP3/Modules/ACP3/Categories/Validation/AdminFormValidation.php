<?php
namespace ACP3\Modules\ACP3\Categories\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\CategoryRepository;
use ACP3\Modules\ACP3\Categories\Validation\ValidationRules\DuplicateCategoryValidationRule;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Categories\Validation
 */
class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var array
     */
    protected $file = [];
    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var int
     */
    protected $categoryId = 0;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\I18n\Translator                             $translator
     * @param \ACP3\Core\Validation\Validator                        $validator
     * @param \ACP3\Modules\ACP3\Categories\Model\CategoryRepository $categoryRepository
     */
    public function __construct(
        Core\I18n\Translator $translator,
        Core\Validation\Validator $validator,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($translator, $validator);

        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param array $file
     *
     * @return AdminFormValidation
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @param array $settings
     *
     * @return AdminFormValidation
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param int $categoryId
     *
     * @return AdminFormValidation
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('categories', 'title_to_short')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'description',
                    'message' => $this->translator->t('categories', 'description_to_short')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\PictureValidationRule::NAME,
                [
                    'data' => $this->file,
                    'field' => 'picture',
                    'message' => $this->translator->t('categories', 'invalid_image_selected'),
                    'extra' => [
                        'width' => $this->settings['width'],
                        'height' => $this->settings['height'],
                        'filesize' => $this->settings['filesize'],
                        'required' => false
                    ]
                ])
            ->addConstraint(
                DuplicateCategoryValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('categories', 'category_already_exists'),
                    'extra' => [
                        'module_id' => empty($this->categoryId) ? $formData['module'] : $this->categoryRepository->getModuleIdByCategoryId($this->categoryId),
                        'category_id' => $this->categoryId
                    ]
                ]);

        if (empty($categoryId)) {
            $this->validator->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'module',
                    'message' => $this->translator->t('categories', 'select_module')
                ]);
        }

        $this->validator->validate();
    }
}
