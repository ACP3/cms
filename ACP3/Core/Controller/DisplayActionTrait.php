<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class DisplayActionTrait
 * @package ACP3\Core\Controller
 */
trait DisplayActionTrait
{
    /**
     * Outputs the requested module controller action
     *
     * @param Response|string|array $actionResult
     * @return Response
     */
    public function display($actionResult): Response
    {
        return $this->getActionResultFactory()->create($actionResult);
    }

    /**
     * @return ResultResponseFactory
     */
    abstract protected function getActionResultFactory(): ResultResponseFactory;
}
