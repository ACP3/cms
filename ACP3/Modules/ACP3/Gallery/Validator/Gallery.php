<?php
namespace ACP3\Modules\ACP3\Gallery\Validator;

use ACP3\Core;

/**
 * Class Gallery
 * @package ACP3\Modules\ACP3\Gallery\Validator
 */
class Gallery extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Date
     */
    protected $dateValidator;
    /**
     * @var Core\Validator\Rules\Router\Aliases
     */
    protected $aliasesValidator;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    /**
     * @param Core\Lang                           $lang
     * @param Core\Validator\Rules\Misc           $validate
     * @param Core\Validator\Rules\Router\Aliases $aliasesValidator
     * @param Core\Validator\Rules\Date           $dateValidator
     * @param Core\Modules                        $modules
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router\Aliases $aliasesValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\Modules $modules
    )
    {
        parent::__construct($lang, $validate);

        $this->aliasesValidator = $aliasesValidator;
        $this->dateValidator = $dateValidator;
        $this->modules = $modules;
    }

    /**
     * @param array  $formData
     * @param string $uriAlias
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $uriAlias = '')
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $this->errors['date'] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $this->errors['title'] = $this->lang->t('gallery', 'type_in_gallery_title');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], $uriAlias) === true) {
            $this->errors['alias'] = $this->lang->t('seo', 'alias_unallowed_characters_or_exists');
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
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $this->errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if ($this->validate->isNumber($formData['sidebar']) === false) {
            $this->errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['overlay']) || $formData['overlay'] != 1 && $formData['overlay'] != 0) {
            $this->errors['overlay'] = $this->lang->t('gallery', 'select_use_overlay');
        }
        if ($this->modules->isActive('comments') === true && (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)) {
            $this->errors['comments'] = $this->lang->t('gallery', 'select_allow_comments');
        }
        if ($this->validate->isNumber($formData['thumbwidth']) === false || $this->validate->isNumber($formData['width']) === false || $this->validate->isNumber($formData['maxwidth']) === false) {
            $this->errors[] = $this->lang->t('gallery', 'invalid_image_width_entered');
        }
        if ($this->validate->isNumber($formData['thumbheight']) === false || $this->validate->isNumber($formData['height']) === false || $this->validate->isNumber($formData['maxheight']) === false) {
            $this->errors[] = $this->lang->t('gallery', 'invalid_image_height_entered');
        }
        if ($this->validate->isNumber($formData['filesize']) === false) {
            $this->errors['filesize'] = $this->lang->t('gallery', 'invalid_image_filesize_entered');
        }

        $this->_checkForFailedValidation();
    }
}
