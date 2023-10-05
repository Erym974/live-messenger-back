<?php

namespace App\Controller\Api;

use App\Entity\Group;
use App\Entity\Message;
use App\Entity\Reaction;
use App\Entity\User;
use App\Service\MessageService;
use App\Service\MessageStatus;
use App\Service\ResponseService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\Collection;

#[isGranted('JWT_HEADER_ACCESS')]
class MessagesController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private ResponseService $responseService)
    {
        
    }

    #[Route('api/messages/{group?}', name: 'api.messages', methods: ['GET', 'POST'])]
    public function messages(?Group $group, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        if($request->getMethod() == 'POST') {

            $parameters = json_decode($request->getContent(), true);

            if(!array_key_exists('group', $parameters)) $this->responseService->ReturnError(400, "Missing parameters");

            /** @var Group */
            $group = $this->em->getRepository(Group::class)->find($parameters['group']);
            if(!$group) return $this->responseService->ReturnError(404, "Group not found");
            if(!$group->hasMember($user)) return $this->responseService->ReturnError(403, "You are not a member of this group");
            if(strlen($parameters['message']) > 300) return $this->responseService->ReturnError(400, "Message is too long");

            $message = new Message();
            $message->setSender($user);
            if($parameters['message'] === ":emoji:") $message->setContent($group->getEmoji());
            else $message->setContent($parameters['message']);
            $message->setStatus(MessageStatus::SENDED);
            $message->setEdited(false);
            $message->setGroup($group);

            $group->setLastMessage($message);

            $this->em->persist($group);
            $this->em->persist($message);
            $this->em->flush();

            return $this->responseService->ReturnSuccess($message, ['groups' => 'messages:read']);

        }

        if(!$group) return $this->responseService->ReturnError(404, "Group not found");

        // if limit exist in query
        if($request->query->get('limit')) {
            $limit = $request->query->get('limit');
        } else {
            $limit = 50;
        }

        $messages = $group->getMessages();

        if(count($messages) > $limit) {
            $messages = array_slice($messages->toArray(), count($messages) - $limit, $limit);
        }

        foreach($messages as $message) {
            foreach($message->getReactions() as $reaction) {
                $reaction->setReacted($reaction->getUsers()->contains($user));
                $reaction->setCount();
            }
        }

        return $this->responseService->ReturnSuccess($messages, ['groups' => 'messages:read']);

    }

    #[Route('api/message', name: 'api.messages.message', methods: ['GET', 'PATCH'])]
    public function messages_edit(Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $params = json_decode($request->getContent(), true);

        /** @var Message */
        $message = $this->em->getRepository(Message::class)->find($params['id']);

        if(!$message) return $this->responseService->ReturnError(404, "Message not found");

        if(isset($params['status'])) {

            if($message->getStatus() == MessageStatus::DELETED || $message->getSender() != $user) return $this->responseService->ReturnError(403, "You can't edit this message");

            switch($params['status']){
                case MessageStatus::DELETED:
                    $message->setStatus(MessageStatus::DELETED);
                    break;
                default:
                    $message->setStatus(MessageStatus::SENDED);
                    break;
            }
        }

        if(isset($params['content'])) {
            if($message->getStatus() == MessageStatus::DELETED || $message->getSender() != $user) return $this->responseService->ReturnError(403, "You can't edit this message");

            $message->setContent($params['content']);
            $message->setEdited(true);
        }

        if(isset($params['reaction'])) {

            if($message->getStatus() == MessageStatus::DELETED) return $this->responseService->ReturnError(403, "You can't edit this message");
            MessageService::AddReaction($message, $params['reaction'], $user, $this->em);

        }

        $this->em->persist($message);
        $this->em->flush();

        foreach ($message->getReactions() as $react) {
            $react->setReacted($react->getUsers()->contains($user));
            $react->setCount(count($react->getUsers()));
        }

        return $this->responseService->ReturnSuccess($message, ['groups' => 'messages:read']);

    }

}
