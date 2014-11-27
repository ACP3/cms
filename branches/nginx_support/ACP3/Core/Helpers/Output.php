<?php
namespace ACP3\Core\Helpers;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Output
 * @package ACP3\Core\Helpers
 */
class Output
{
    /**
     * @param array $data
     */
    public function outputJson(array $data)
    {
        $response = new JsonResponse($data);
        $response->send();
        exit;
    }
}
