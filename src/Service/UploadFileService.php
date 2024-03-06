<?php

namespace App\Service;

use App\Entity\File;
use App\Service\AbstractService;
use Symfony\Component\HttpFoundation\JsonResponse;

class UploadFileServiceResponse {

    public function __construct(
        public bool $status,
        public string $message,
        public ?File $file
    ) {}

    public function getStatus(): bool {return $this->status;}
    public function getMessage(): string {return $this->message;}
    public function getFile(): File {return $this->file;}

}

class UploadFileService extends AbstractService {

    private array $allowedMimeTypes = ["image/jpeg", "image/png"];

    public function uploadFile($fileToUpload, $uploadPath) : UploadFileServiceResponse
    {

        if(!in_array($fileToUpload->getMimeType(), $this->allowedMimeTypes)) return new UploadFileServiceResponse(false, "File is not an valide image", null);
        if($fileToUpload->getSize() > 1000000) return new UploadFileServiceResponse(false, "File is too big", null);

        $filename = md5(uniqid()) . "." . $fileToUpload->guessExtension();
        $type = $fileToUpload->getClientmimeType();
        $fileToUpload->move($this->getParameter($uploadPath), $filename);

        $parent = null;

        switch($uploadPath) {
            case "users_upload_directory":
                $parent = "users";
                break;
            case "posts_upload_directory":
                $parent = "posts";
                break;
            case "messages_upload_directory":
                $parent = "messages";
                break;
        }

        $file = (new File())
            ->setParent($parent)
            ->setName($filename)
            ->setType($type)
            ->setPath("/" . $filename);

        $this->em->persist($file);
        $this->em->flush();

        return new UploadFileServiceResponse(true, "File uploaded", $file);

    }

}