<?php
namespace ACP3\Modules\Search;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Search
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validateFormKey();

        $errors = [];
        if (strlen($formData['search_term']) < 3) {
            $errors['search-term'] = $this->lang->t('search', 'search_term_to_short');
        }
        if (empty($formData['mods'])) {
            $errors['mods'] = $this->lang->t('search', 'no_module_selected');
        }
        if (empty($formData['area'])) {
            $errors['area'] = $this->lang->t('search', 'no_area_selected');
        }
        if (empty($formData['sort']) || $formData['sort'] != 'asc' && $formData['sort'] != 'desc') {
            $errors['sort'] = $this->lang->t('search', 'no_sorting_selected');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }
}
