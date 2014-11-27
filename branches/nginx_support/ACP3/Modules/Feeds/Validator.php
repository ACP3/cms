<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Feeds
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['feed_type']) || in_array($formData['feed_type'], ['RSS 1.0', 'RSS 2.0', 'ATOM']) === false) {
            $this->errors['feed-type'] = $this->lang->t('feeds', 'select_feed_type');
        }

        $this->_checkForFailedValidation();
    }
}
