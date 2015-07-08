<?php
namespace ACP3\Modules\ACP3\Search;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Search
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->errors = [];
        if (strlen($formData['search_term']) < 4) {
            $this->errors['search-term'] = $this->lang->t('search', 'search_term_to_short');
        }
        if (empty($formData['mods'])) {
            $this->errors['mods'] = $this->lang->t('search', 'no_module_selected');
        }
        if (empty($formData['area'])) {
            $this->errors['area'] = $this->lang->t('search', 'no_area_selected');
        }
        if (empty($formData['sort']) || $formData['sort'] != 'asc' && $formData['sort'] != 'desc') {
            $this->errors['sort'] = $this->lang->t('search', 'no_sorting_selected');
        }

        $this->_checkForFailedValidation();
    }
}
