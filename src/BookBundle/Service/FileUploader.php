<?php

namespace BookBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetTitleDir;
    private $targetBookDir;

    public function __construct($targetTitleDir, $targetBookDir)
    {
        $this->targetTitleDir = $targetTitleDir;
        $this->targetBookDir = $targetBookDir;
    }

    public function uploadTitle(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->targetTitleDir, $fileName);

        return $fileName;
    }

    public function uploadBook(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->targetBookDir, $fileName);

        return $fileName;
    }

    public function removeBook(UploadedFile $file)
    {
        unlink($this->targetBookDir, $file->getFilename()); // delete full-size image
        return $file->getFilename();
    }

    public function removeTitle(UploadedFile $file)
    {
        unlink($this->targetTitleDir, $file->getFilename()); // delete full-size image
        return $file->getFilename();
    }

    public function getTargetTitleDir()
    {
        return $this->targetTitleDir;
    }

    public function getTargetBookDir()
    {
        return $this->targetBookDir;
    }
}