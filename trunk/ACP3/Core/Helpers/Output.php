<?php
namespace ACP3\Core\Helpers;


use Symfony\Component\HttpFoundation\JsonResponse;

class Output
{
    /**
     * @param array $data
     */
    public function outputJson(array $data)
    {
        new JsonResponse($data);
        exit;
    }

}