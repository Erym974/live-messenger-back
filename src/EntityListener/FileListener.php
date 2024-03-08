<?php

namespace App\EntityListener;

use App\Entity\File;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileListener
{

    public function __construct(private ParameterBagInterface $parameters)
    {
        
    }

    public function preRemove(File $file) {
        $upload_path = $this->parameters->get('upload_directory') . $file->getParent() . "/" . $file->getName();
        if(file_exists($upload_path)) unlink($upload_path);
    }

}