<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Installer\Schema;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Categories\Validation\ValidationRules\AllowedSuperiorCategoryValidationRule;
use ACP3\Modules\ACP3\Categories\Validation\ValidationRules\DuplicateCategoryValidationRule;
use ACP3\Modules\ACP3\Categories\Validation\ValidationRules\ParentIdValidationRule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    protected ?UploadedFile $file = null;

    protected int $categoryId = 0;

    public function __construct(
        protected Core\Settings\SettingsInterface $settings,
        Core\I18n\Translator $translator,
        Core\Validation\Validator $validator,
        protected CategoryRepository $categoryRepository
    ) {
        parent::__construct($translator, $validator);
    }

    /**
     * @return $this
     */
    public function setFile(?UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return static
     */
    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \ACP3\Core\Validation\Exceptions\ValidationFailedException
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function validate(array $formData): void
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('categories', 'title_to_short'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\PictureValidationRule::class,
                [
                    'data' => $this->file,
                    'field' => 'picture',
                    'message' => $this->translator->t('categories', 'invalid_image_selected'),
                    'extra' => [
                        'width' => $settings['width'],
                        'height' => $settings['height'],
                        'filesize' => $settings['filesize'],
                        'required' => false,
                    ],
                ]
            )
            ->addConstraint(
                DuplicateCategoryValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('categories', 'category_already_exists'),
                    'extra' => [
                        'module_id' => empty($this->categoryId) ? $formData['module_id'] : $this->categoryRepository->getModuleIdByCategoryId($this->categoryId),
                        'category_id' => $this->categoryId,
                    ],
                ]
            )
            ->addConstraint(
                ParentIdValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'parent_id',
                    'message' => $this->translator->t('categories', 'select_superior_category'),
                ]
            )
            ->addConstraint(
                AllowedSuperiorCategoryValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['parent_id', 'module_id'],
                    'message' => $this->translator->t('categories', 'superior_category_not_allowed'),
                ]
            );

        if (empty($this->categoryId)) {
            $this->validator->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'module_id',
                    'message' => $this->translator->t('categories', 'select_module'),
                ]
            );
        }

        $this->validator->validate();
    }
}
