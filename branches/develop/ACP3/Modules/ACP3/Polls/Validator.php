<?php
namespace ACP3\Modules\ACP3\Polls;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Polls
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Date
     */
    protected $dateValidator;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\Date $dateValidator
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Date $dateValidator
    ) {
        parent::__construct($lang, $validate);

        $this->dateValidator = $dateValidator;
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $this->errors['date'] = $this->lang->t('system', 'select_date');
        }
        if (empty($formData['title'])) {
            $this->errors['title'] = $this->lang->t('polls', 'type_in_question');
        }
        $i = 0;
        foreach ($formData['answers'] as $row) {
            if (!empty($row)) {
                ++$i;
            }
        }
        if ($i <= 2) {
            $this->errors['answer'] = $this->lang->t('polls', 'type_in_two_answers');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $this->errors[] = $this->lang->t('system', 'select_date');
        }
        if (empty($formData['title'])) {
            $this->errors['title'] = $this->lang->t('polls', 'type_in_question');
        }
        $markedAnswers = 0;
        $allAnswersEmpty = true;
        foreach ($formData['answers'] as $row) {
            if (!empty($row['value'])) {
                $allAnswersEmpty = false;
            }
            if (isset($row['delete'])) {
                ++$markedAnswers;
            }
        }
        if ($allAnswersEmpty === true) {
            $this->errors['answer'] = $this->lang->t('polls', 'type_in_two_answers');
        }
        if (count($formData['answers']) - $markedAnswers < 2) {
            $this->errors['answer'] = $this->lang->t('polls', 'can_not_delete_all_answers');
        }

        $this->_checkForFailedValidation();
    }
}
