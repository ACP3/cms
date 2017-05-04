<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;


use ACP3\Core\Controller\ResultResponse\ActionResultTypeInterface;
use Symfony\Component\HttpFoundation\Response;

class ResultResponseFactory
{
    /**
     * @var ActionResultTypeInterface[]
     */
    private $actionResultTypes = [];

    /**
     * @param ActionResultTypeInterface $actionResultType
     * @return $this
     */
    public function registerActionResultType(ActionResultTypeInterface $actionResultType)
    {
        $this->actionResultTypes[] = $actionResultType;

        return $this;
    }

    /**
     * @param mixed $result
     * @return Response
     */
    public function create($result): Response
    {
        foreach ($this->actionResultTypes as $type) {
            if ($type->supports($result)) {
                return $type->process($result);
            }
        }

        throw new \InvalidArgumentException(
            sprintf('The controller action did returned an unsupported action result (%s).', gettype($result))
        );
    }
}
