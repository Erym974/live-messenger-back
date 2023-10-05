<?php

namespace App\Service;
use App\Service\AbstractService;

class FileService extends AbstractService {

    static function getExtension(string $filename) : string
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    static function getFilename(string $filename) : string
    {
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    public function uploadFile($file, $path) : string
    {
        $timestamp = time();
        $filename = $timestamp . uniqid() . "." . $file->guessExtension();
        $file->move($this->getParameter('upload_directory'), $filename);
        return $filename;
    }

    public function removeFile($filename) {

        if(file_exists($filename)) {
            unlink($filename);
        }

    }

}