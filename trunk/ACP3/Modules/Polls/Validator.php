<?php
namespace ACP3\Modules\Polls;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Polls
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Date
     */
    protected $dateValidator;

    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Date $dateValidator
    )
    {
        parent::__construct($lang, $validate);

        $this->dateValidator = $dateValidator;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (empty($formData['title'])) {
            $errors['title'] = $this->lang->t('polls', 'type_in_question');
        }
        $i = 0;
        foreach ($formData['answers'] as $row) {
            if (!empty($row)) {
                ++$i;
            }
        }
        if ($i <= 1) {
            $errors[] = $this->lang->t('polls', 'type_in_answer');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if ($this->dateValidator->date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (empty($formData['title'])) {
            $errors['title'] = $this->lang->t('polls', 'type_in_question');
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
            $errors[] = $this->lang->t('polls', 'type_in_answer');
        }
        if (count($formData['answers']) - $markedAnswers < 2) {
            $errors[] = $this->lang->t('polls', 'can_not_delete_all_answers');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 