<?php

namespace ACP3\Modules\ACP3\Feeds;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Feeds
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Validator\Validator
     */
    protected $validator;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Validator  $validator
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate
    )
    {
        parent::__construct($lang, $validate);

        $this->validator = $validator;
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
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'feed_type',
                    'message' => $this->lang->t('feeds', 'select_feed_type'),
                    'extra' => [
                        'haystack' => ['RSS 1.0', 'RSS 2.0', 'ATOM']
                    ]
                ]);

        $this->validator->validate();
    }
}
