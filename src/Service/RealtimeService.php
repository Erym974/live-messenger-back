<?php 

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\AbstractService;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;

class RealtimeService extends AbstractService {

    public function __construct(private MessageBusInterface $bus)
    {
        
    }

    /**
     * 
     * @param int $code Code error
     * @param string $message Message error
     * 
     */
    public function publish(string $topic, mixed $datas, Collection $targets) : null
    {
        
        $front = $_ENV['FRONTEND_URL'];
        if(substr($topic, 0, 1) != "/") $topic = "/" . $topic;
        $final_url = $front . $topic;

        // $finalTargets = [];
        
        // foreach($targets as $target){
        //     array_push($finalTargets, $front . "/users/" . $target->getId());
        // }

        $this->bus->dispatch(new Update(
            $final_url,
            $datas,
        ));

        return null;

    }

}