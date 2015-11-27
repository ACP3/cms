<?php
namespace ACP3\Modules\ACP3\Guestbook\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractFloodBarrierValidationRule;

/**
 * Class FloodBarrierValidationRule
 * @package ACP3\Modules\ACP3\Guestbook\Validator\ValidationRules
 */
class FloodBarrierValidationRule extends AbstractFloodBarrierValidationRule
{
    const NAME = 'guestbook_flood_barrier';
}