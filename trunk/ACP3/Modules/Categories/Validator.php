<?php
namespace ACP3\Modules\Categories;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Categories
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
     * @param array $formData
     * @param $file
     * @param $settings
     * @param string $categoryId
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $file, $settings, $categoryId = '')
    {
        $this->validateFormKey();

        $errors = array();
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('categories', 'title_to_short');
        }
        if (strlen($formData['description']) < 3) {
            $errors['description'] = $this->lang->t('categories', 'description_to_short');
        }
        if (!empty($file) && (empty($file['tmp_name']) || empty($file['size']) ||
                $this->mimeValidator->isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
                $_FILES['picture']['error'] !== UPLOAD_ERR_OK)
        ) {
            $errors['picture'] = $this->lang->t('categories', 'invalid_image_selected');
        }
        if (empty($categoryId) && empty($formData['module'])) {
            $errors['module'] = $this->lang->t('categories', 'select_module');
        }

        $categoryName = empty($categoryId) ? $formData['module'] : $this->categoriesModel->getModuleNameFromCategoryId($categoryId);
        if (strlen($formData['title']) >= 3 && $this->categoriesHelpers->categoryIsDuplicate($formData['title'], $categoryName, $categoryId)) {
            $errors['title'] = $this->lang->t('categories', 'category_already_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->validate->isNumber($formData['width']) === false) {
            $errors['width'] = $this->lang->t('categories', 'invalid_image_width_entered');
        }
        if ($this->validate->isNumber($formData['height']) === false) {
            $errors['height'] = $this->lang->t('categories', 'invalid_image_height_entered');
        }
        if ($this->validate->isNumber($formData['filesize']) === false) {
            $errors['filesize'] = $this->lang->t('categories', 'invalid_image_filesize_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

}