<?php
namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Categories
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Mime
     */
    protected $mimeValidator;
    /**
     * @var Model
     */
    protected $categoriesModel;
    /**
     * @var Helpers
     */
    protected $categoriesHelpers;

    /**
     * @param Core\Lang                 $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Mime $mimeValidator
     * @param Helpers                   $categoriesHelpers
     * @param Model                     $categoriesModel
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Mime $mimeValidator,
        Helpers $categoriesHelpers,
        Model $categoriesModel
    )
    {
        parent::__construct($lang, $validate);

        $this->mimeValidator = $mimeValidator;
        $this->categoriesHelpers = $categoriesHelpers;
        $this->categoriesModel = $categoriesModel;
    }

    /**
     * @param array  $formData
     * @param        $file
     * @param array  $settings
     * @param string $categoryId
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $file, array $settings, $categoryId = '')
    {
        $this->validateFormKey();

        $this->errors = [];
        if (strlen($formData['title']) < 3) {
            $this->errors['title'] = $this->lang->t('categories', 'title_to_short');
        }
        if (strlen($formData['description']) < 3) {
            $this->errors['description'] = $this->lang->t('categories', 'description_to_short');
        }
        if (!empty($file) && (empty($file['tmp_name']) || empty($file['size']) ||
                $this->mimeValidator->isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
                $file['error'] !== UPLOAD_ERR_OK)
        ) {
            $this->errors['picture'] = $this->lang->t('categories', 'invalid_image_selected');
        }
        if (empty($categoryId) && empty($formData['module'])) {
            $this->errors['module'] = $this->lang->t('categories', 'select_module');
        }

        $categoryName = empty($categoryId) ? $formData['module'] : $this->categoriesModel->getModuleNameFromCategoryId($categoryId);
        if (strlen($formData['title']) >= 3 && $this->categoriesHelpers->categoryIsDuplicate($formData['title'], $categoryName, $categoryId)) {
            $this->errors['title'] = $this->lang->t('categories', 'category_already_exists');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->validate->isNumber($formData['width']) === false) {
            $this->errors['width'] = $this->lang->t('categories', 'invalid_image_width_entered');
        }
        if ($this->validate->isNumber($formData['height']) === false) {
            $this->errors['height'] = $this->lang->t('categories', 'invalid_image_height_entered');
        }
        if ($this->validate->isNumber($formData['filesize']) === false) {
            $this->errors['filesize'] = $this->lang->t('categories', 'invalid_image_filesize_entered');
        }

        $this->_checkForFailedValidation();
    }
}
