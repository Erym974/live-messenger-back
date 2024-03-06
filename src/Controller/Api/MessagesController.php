<?php

namespace App\Controller\Api;

use App\Entity\File;
use App\Entity\Group;
use App\Entity\Message;
use App\Entity\Reaction;
use App\Entity\User;
use App\Service\GroupService;
use App\Service\MessageService;
use App\Service\MessageStatus;
use App\Service\ResponseService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;


class MessagesController extends AbstractController
{

    public function __construct(private LoggerInterface $logger, private GroupService $groupService, private SerializerInterface $serializer, private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private ResponseService $responseService)
    {
    }

    #[Route('api/messages/{group}', name: 'api.messages.post', methods: ['POST'])]
    public function messages_post(Group $group, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();
        if (!$group->hasMember($user)) return $this->responseService->ReturnError(403, "You are not a member of this group");

        $parameters = json_decode($request->getContent(), true);
        if (!$parameters) $parameters = $request->request->all();

        if (strlen($parameters['message']) > 300) return $this->responseService->ReturnError(400, "Message is too long");

        // Initialize message
        $message = (new Message())
            ->setSender($user)
            ->setStatus(MessageStatus::SENDED)
            ->setEdited(false)
            ->setGroup($group);

        if ($parameters['message'] === ":emoji:") $message->setContent($group->getEmoji());
        else $message->setContent($parameters['message']);

        if (isset($parameters['reply'])) {
            $reply = $this->em->getRepository(Message::class)->find($parameters['reply']);
            if ($reply) $message->setReply($reply);
        }

        $group->setLastMessage($message);

        $this->em->persist($group);
        $this->em->persist($message);
        $this->em->flush();

        $files = $request->files->get('files');

        if ($files) {

            $filesMessage = null;
            $dedicatedMessage = false;

            if ($parameters['message']) {
                $filesMessage = new Message();
                $filesMessage->setStatus(MessageStatus::SENDED);
                $filesMessage->setSender($user);
                $filesMessage->setContent("");
                $filesMessage->setEdited(false);
                $filesMessage->setGroup($group);
                $dedicatedMessage = true;

                $this->em->persist($filesMessage);
                $this->em->flush();
                $group->setLastMessage($filesMessage);
            } else {
                $filesMessage = $message;
            }

            foreach ($files as $fileData) {

                $filename = md5(uniqid()) . "." . $fileData->guessExtension();
                $type = $fileData->getClientmimeType();
                $fileData->move($this->getParameter('messages_upload_directory'), $filename);
                $file = new File();
                $file->setName($filename);
                $file->setType($type);
                $file->setPath("/" . $filename);
                $filesMessage->addFile($file);
                $this->em->persist($filesMessage);
                $this->em->persist($file);
                $this->em->flush();
            }
        }

        $responseMessages = [$message];

        if (isset($dedicatedMessage) && $dedicatedMessage) $responseMessages[] = $filesMessage;

        foreach ($group->getMembers() as $member) {
            if ($member->getId() != $user->getId()) {
                $group = $this->groupService->parseDatas($group, $member);
            }
        }

        return $this->responseService->ReturnSuccess($message, ['groups' => 'messages:read']);
    }

    #[Route('api/messages/{group}', name: 'api.messages.get', methods: ['GET'])]
    public function messages_get(Group $group, Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        if ($request->query->get('limit')) $limit = intval($request->query->get('limit'));
        else $limit = 10;

        if ($request->query->get('page')) {
            $page = intval($request->query->get('page'));
            if ($page < 1) $page = 1;
        } else {
            $page = 1;
        }

        $messages = $this->em->getRepository(Message::class)->findMessagesOfGroup($group, $limit, $page);
        $total = $group->getMessages()->count();

        $messages = array_reverse($messages);

        foreach ($messages as $message) {
            foreach ($message->getReactions() as $reaction) {
                $reaction->setReacted($reaction->getUsers()->contains($user));
                $reaction->setCount();
            }
        }

        $maxPage = ceil($total / $limit);

        return $this->responseService->ReturnSuccess(["total" => $total, 'size' => count($messages), 'pages' => $maxPage, 'messages' => $messages], ['groups' => 'messages:read']);
    }

    #[Route('api/message', name: 'api.messages.message', methods: ['GET', 'PATCH'])]
    public function messages_edit(Request $request): JsonResponse
    {

        /** @var User */
        $user = $this->getUser();

        $params = json_decode($request->getContent(), true);

        /** @var Message */
        $message = $this->em->getRepository(Message::class)->find($params['id']);

        if (!$message) return $this->responseService->ReturnError(404, "Message not found");

        if (isset($params['status'])) {

            if ($message->getStatus() == MessageStatus::DELETED || $message->getSender() != $user) return $this->responseService->ReturnError(403, "You can't edit this message");

            switch ($params['status']) {
                case MessageStatus::DELETED:
                    $message->setStatus(MessageStatus::DELETED);
                    break;
                default:
                    $message->setStatus(MessageStatus::SENDED);
                    break;
            }
        }

        if (isset($params['content'])) {
            if ($message->getStatus() == MessageStatus::DELETED || $message->getSender() != $user) return $this->responseService->ReturnError(403, "You can't edit this message");

            $message->setContent($params['content']);
            $message->setEdited(true);
        }

        if (isset($params['reaction'])) {

            if ($message->getStatus() == MessageStatus::DELETED) return $this->responseService->ReturnError(403, "You can't edit this message");
            MessageService::AddReaction($message, $params['reaction'], $user, $this->em);
        }

        $this->em->persist($message);
        $this->em->flush();

        foreach ($message->getReactions() as $react) {
            $react->setReacted($react->getUsers()->contains($user));
            $react->setCount(count($react->getUsers()));
        }

        /** @var Group */
        $group = $message->getGroup();

        return $this->responseService->ReturnSuccess($message, ['groups' => 'messages:read']);
    }
}
