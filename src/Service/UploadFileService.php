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

    public function uploadFile($fileToUpload, string $uploadPath, array $allowedType = ["png", "jpeg", "gif", "jpg"]) : UploadFileServiceResponse
    {

        if($fileToUpload == null) return new UploadFileServiceResponse(false, "No file to upload", null);
        if($fileToUpload->getSize() > 5000000 || $fileToUpload->getError() === 1) return new UploadFileServiceResponse(false, "File size may not exceed 5MB", null);

        if(!in_array($fileToUpload->guessExtension(), $allowedType)) return new UploadFileServiceResponse(false, "File type not allowed", null);

        $filename = md5(uniqid()) . "." . $fileToUpload->guessExtension();
        $type = $fileToUpload->getClientmimeType();
        try {
            $fileToUpload->move($this->getParameter($uploadPath), $filename);
        } catch (\Exception $e) {
            return new UploadFileServiceResponse(false, "An error occurred when writing the file on the server", null);
        }

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
            ->setPath($this->getParameter("ressources_url") . "/" . $parent . "/" . $filename);

        $this->em->persist($file);
        $this->em->flush();

        return new UploadFileServiceResponse(true, "File uploaded", $file);

    }

}