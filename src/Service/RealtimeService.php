<?php 

namespace App\Service;

use App\Entity\Group;
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
    public function publish($topic, string $datas) : null
    {
        $realtime = $_ENV['REALTIME_METHOD'];

        if($realtime != "MERCURE" && $realtime != "SOCKET") return null;

        if($realtime === "MERCURE") {

            $front = $_ENV['FRONTEND_URL'];

            $topics = [];

            // if topic is an array
            if(is_array($topic)) {
                foreach($topic as $t) {
                    if(substr($t, 0, 1) != "/") $t = "/" . $t;
                    $topics[] = $front . $t;
                }
                $topic = $topics;
            } else {
                if(substr($topic, 0, 1) != "/") $topic = "/" . $topic;
                $topics[] = $front . $topic;
            }

            $update = new Update(
                $topics,
                $datas,
                false
            );
            $this->bus->dispatch($update);
            return null;

        }

        if($realtime === "SOCKET") {
            
        }

    }

    public function getTopicsGroupUpdate(string $type, Group $group, ?User $user = null) : array
    {
        $topics = [];
        foreach($group->getMembers() as $member) {
            if($user && ($member->getId() != $user->getId())) {
                $topics[] = "user/" . $member->getId() . "/messenger/" . $group->getId() . "/" . $type;
            } else {
                $topics[] = "user/" . $member->getId() . "/messenger/" . $group->getId() . "/" . $type;
            }

        }
        return $topics;
    }

    public function getTopicsAsideNewMessage(Group $group, ?User $user = null) : array
    {
        $topics = [];
        foreach($group->getMembers() as $member) {
            if($user && ($member->getId() != $user->getId())) {
                $topics[] = "user/" . $member->getId() . "/new-message";
            } else {
                $topics[] = "user/" . $member->getId() . "/new-message";
            }

        }
        return $topics;
    }

}