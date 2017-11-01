<?php

namespace BookBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use BookBundle\Entity\Book;
use BookBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UploadSubscriber implements EventSubscriber
{
    private $uploaderTitle;
    private $uploaderBook;

    public function __construct(FileUploader $uploaderTitle, FileUploader $uploaderBook)
    {
        $this->uploaderTitle = $uploaderTitle;
        $this->uploaderBook = $uploaderBook;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'uploadFileTitle',
            'uploadFileBook',
            'postLoad',
            'preRemoveUpload',
            'removeUpload',
            'postRemove',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFileTitle($entity);
        $this->uploadFileBook($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFileTitle($entity);
        $this->uploadFileBook($entity);
    }

    private function uploadFileTitle($entity)
    {
        // загрузка работает только для сущностей Book
        if (!$entity instanceof Book) {
            return;
        }

        $fileTitle = $entity->getTitle();

        // загружать только новые файлы
        if (!$fileTitle instanceof UploadedFile) {
            return;
        }

        $entity->setTitleOrig($_FILES['book']['name']['title']);

        $fileTitleName = $this->uploaderTitle->uploadTitle($fileTitle);
        $entity->setTitle($fileTitleName);
    }

    private function uploadFileBook($entity)
    {
        // загрузка работает только для сущностей Book
        if (!$entity instanceof Book) {
            return;
        }

        $fileBook = $entity->getBook();

        if (!$fileBook instanceof UploadedFile) {
            return;
        }
        $entity->setBookOrig($_FILES['book']['name']['book']);

        $fileBookName = $this->uploaderBook->uploadBook($fileBook);
        $entity->setBook($fileBookName);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Book) {
            return;
        }

        if ($fileTitleName = $entity->getTitle()) {
            $entity->setTitle(new File($this->uploaderTitle->getTargetTitleDir().'/'.$fileTitleName));
        }

        if ($fileBookName = $entity->getBook()) {
            $entity->setBook(new File($this->uploaderBook->getTargetBookDir().'/'.$fileBookName));
        }
    }

    public function preRemoveUpload(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Book) {
            return;
        }

        $this->uploaderTitle = $this->getAbsolutePath().'titles/'.$entity->getTitle();
        $this->uploaderBook = $this->getAbsolutePath().'titles/'.$entity->getBook();
    }

    public function removeUpload($entity)
    {
        if (!$entity instanceof Book) {
            return;
        }

        if($file = $this->getAbsolutePath().'titles/'.$entity->getTitle())
            unlink($file);
        if($file = $this->getAbsolutePath().'books/'.$entity->getBook())
            unlink($file);
    }

    private function getAbsolutePath()
    {
        return '/uploads/';
    }

    public function postRemove($entity)
    {
        // загрузка работает только для сущностей Book
        if (!$entity instanceof Book) {
            return;
        }

        $fileTitle = $entity->getTitle();
        $fileBook = $entity->getBook();

        $this->uploaderTitle->removeTitle($fileTitle);
        $this->uploaderBook->removeBook($fileBook);
    }
}