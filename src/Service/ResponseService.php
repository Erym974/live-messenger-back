<?php 

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\AbstractService;

class ResponseService extends AbstractService {

    /**
     * 
     * @param int $code Code error
     * @param string $message Message error
     * 
     */
    public function ReturnError(int $code = 400, string $message = "An error occured") : JsonResponse
    {
        
        return new JsonResponse(
            [
                "status" => false,
                "message" => $message,
            ],
            $code,
            ['Content-Type' => "application/json"]
        );

    }

    /**
     * 
     * @param any $datas Datas data to return
     * @param array $groups Groups to serialize
     * 
     */
    public function ReturnSuccess(mixed $datas = [], array $groups = []) : JsonResponse
    {
        return $this->json(
            [
                "status" => true,
                "datas" => $datas,
            ],
            200,
            ['Content-Type' => "application/json"],
            $groups,
        );
    }

}