<?php

namespace BookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Cache\Simple\FilesystemCache;


/**
 * Book
 *
 * @ORM\Table(name="book")
 * @ORM\Entity(repositoryClass="BookBundle\Repository\BookRepository")
 */
class Book
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(name="title", type="string")
     *
     * @Assert\NotBlank(message="Please, upload the title as a PNG or JPG file.")
     * @Assert\File( mimeTypes={ "image/png", "image/jpg", "image/jpeg" })
     */
    private $title;

    /**
     * @ORM\Column(name="book", type="string")
     *
     * @Assert\NotBlank(message="Please, upload the brochure as a PDF file.")
     * @Assert\File(
     *     maxSize="5M",
     *     mimeTypes={ "application/pdf", "application/rtf" })
     */
    private $book;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRead", type="datetime")
     */
    private $dateRead;

    /**
     * @var bool
     *
     * @ORM\Column(name="isDownload", type="boolean")
     */
    private $isDownload;

    private $cache;

    /**
     * @return FilesystemCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param FilesystemCache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Book
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get book1
     *
     * @return string
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set book1
     *
     * @param string $book
     *
     * @return Book
     */
    public function setBook($book)
    {
        $this->book = $book;

        return $this;
    }


    /**
     * Set dateRead
     *
     * @param \DateTime $dateRead
     *
     * @return Book
     */
    public function setDateRead($dateRead)
    {
        $this->dateRead = $dateRead;

        return $this;
    }

    /**
     * Get dateRead
     *
     * @return \DateTime
     */
    public function getDateRead()
    {
        return $this->dateRead;
    }

    /**
     * Set isDownload
     *
     * @param boolean $isDownload
     *
     * @return Book
     */
    public function setIsDownload($isDownload)
    {
        $this->isDownload = $isDownload;

        return $this;
    }

    /**
     * Get isDownload
     *
     * @return bool
     */
    public function getIsDownload()
    {
        return $this->isDownload;
    }
}
