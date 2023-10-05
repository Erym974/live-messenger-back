<?php 

namespace App\Service;

use App\Entity\Message;
use App\Entity\Reaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\AbstractService;

class MessageService extends AbstractService {

    /**
     * 
     * @param Message $message Message to edit
     * @param string $reaction React to add
     * @param User $user User who react
     * @param EntityManagerInterface $em Entity manager
     * 
     */
    static function AddReaction(Message $message, string $reactionToAdd, User $user, EntityManagerInterface $em) : Message
    {
        
        $reactions = $message->getReactions();
        $delete = false;
        $already = false;

        foreach ($reactions as $reaction) {
            if ($reaction->getUsers()->contains($user)) {
                if ($reaction->getContent() === $reactionToAdd) $delete = true;
                $reaction->removeUser($user);
                $em->persist($reaction);
            }

            if (count($reaction->getUsers()) == 0) {
                $message->removeReaction($reaction);
                $em->remove($reaction);
                $em->flush();
            }
    
            if ($reaction->getContent() === $reactionToAdd && !$delete) {
                $reaction->addUser($user);
                $em->persist($reaction);
                $em->flush();
                $already = true;
            }
        }

        $reaction = null;

        if (!$delete && !$already) {
            $reaction = new Reaction();
            $reaction->setContent($reactionToAdd);
            $reaction->addUser($user);

            if(count($message->getReactions()) > 0) $message->addReaction($reaction);
            else $message->setReactions([$reaction]);

            $em->persist($reaction);
            $em->persist($message);
            $em->flush();
        }

        return $message;

    }

    /**
     * 
     * @param any $datas Datas data to return
     * @param array $groups Groups to serialize
     * 
     */
    static function ReturnSuccess($datas = [], array $groups = null) : JsonResponse
    {
        
        $group = $groups ? ['groups' => $groups] : false;

        return new JsonResponse(
            [
                "status" => true,
                "datas" => $datas,
            ],
            200,
            ['Content-Type' => "application/json"],
            $group,
        );

    }

}