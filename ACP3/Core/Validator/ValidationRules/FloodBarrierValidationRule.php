<?php
namespace ACP3\Core\Validator\ValidationRules;
use ACP3\Core\Date;

/**
 * Class FloodBarrierValidationRule
 * @package ACP3\Core\Validator\ValidationRules
 */
class FloodBarrierValidationRule extends AbstractValidationRule
{
    const NAME = 'flood_barrier';

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;

    /**
     * FloodBarrierValidationRule constructor.
     *
     * @param \ACP3\Core\Date $date
     */
    public function __construct(Date $date)
    {
        $this->date = $date;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        $floodTime = !empty($extra['last_date']) ? $this->date->timestamp($extra['last_date'], true) + 30 : 0;
        $time = $this->date->timestamp('now', true);

        return $floodTime <= $time;
    }
}