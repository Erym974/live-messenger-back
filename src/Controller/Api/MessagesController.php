<?php

namespace App\Controller\Api;

use App\Entity\File;
use App\Entity\Group;
use App\Entity\Message;
use App\Entity\User;
use App\Service\GroupService;
use App\Service\MessageService;
use App\Service\MessageStatus;
use App\Service\ResponseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;


class MessagesController extends AbstractController
{

    public function __construct(private GroupService $groupService, private SerializerInterface $serializer, private EntityManagerInterface $em, private UserPasswordHasherInterface $hasher, private ResponseService $responseService)
    {
    }

    #[Route('messages/{group}', name: 'api.messages.post', methods: ['POST'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
    public function messages_post(Group $group, Request $request, LoggerInterface $logger): JsonResponse
    {

        

        /** @var User */
        $user = $this->getUser();
        if (!$group->hasMember($user)) return $this->responseService->ReturnError(403, "You are not a member of this group");

        $parameters = json_decode($request->getContent(), true);
        if (!$parameters) $parameters = $request->request->all();

        $content = $parameters['message'] ?? "";
        $files = $parameters['files'] ?? [];
        if(!$content && count($files) == 0) return $this->responseService->ReturnError(400, "Missing parameters");

        if (strlen($content) > 300) return $this->responseService->ReturnError(400, "Message is too long");

        $message = (new Message())
            ->setSender($user)
            ->setStatus(MessageStatus::SENDED)
            ->setEdited(false)
            ->setGroup($group);

        if ($content === ":emoji:") $message->setContent($group->getEmoji());
        else $message->setContent($content);

        if (isset($parameters['reply']) && count($files) === 0) {
            $reply = $this->em->getRepository(Message::class)->find($parameters['reply']);
            if ($reply) $message->setReply($reply);
        }

        $group->setLastMessage($message);

        $this->em->persist($group);
        $this->em->persist($message);
        $this->em->flush();

        if (count($files) > 0) {
            foreach ($files as $fileData) {

                // Convert base 64 to file
                $base64WithoutHeader = explode('base64', $fileData)[1];
                $decodedFileData = base64_decode($base64WithoutHeader);
                $extension = explode('/', mime_content_type($fileData))[1];
                $type = mime_content_type($fileData);
                $filename = md5(uniqid()) . ".$extension";
                file_put_contents($this->getParameter('kernel.project_dir') . '/public/uploads/messages/' . $filename, $decodedFileData);

                // Create File entity
                $file = (new File())
                    ->setParent("messages")
                    ->setName($filename)
                    ->setType($type)
                    ->setPath($this->getParameter("ressources_url") . "/messages/" . $filename);
                $message->addFile($file);

                // Save entities
                $this->em->persist($message);
                $this->em->persist($file);
                $this->em->flush();
            }
        }

        return $this->responseService->ReturnSuccess($message, ['groups' => 'messages:read']);
    }

    #[Route('messages/{group}', name: 'api.messages.get', methods: ['GET'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
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

        return $this->responseService->ReturnSuccess(["total" => $total, 'size' => count($messages), 'page' => $page, 'pages' => $maxPage, 'messages' => $messages], ['groups' => 'messages:read']);
    }

    #[Route('message', name: 'api.messages.message', methods: ['GET', 'PATCH'], host: 'api.swiftchat.{extension}', defaults: ['extension' => '%default_extension%'], requirements: ['extension' => '%default_extension%'])]
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

        return $this->responseService->ReturnSuccess($message, ['groups' => 'messages:read']);
    }
}
