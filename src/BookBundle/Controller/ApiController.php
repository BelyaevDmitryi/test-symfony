<?php

namespace BookBundle\Controller;

use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use BookBundle\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * Lists all book entities.
     *
     * @Route("/select", name="select")
     * @Method("GET")
     */
    public function selectAllAction()
    {
        $cache = new FilesystemCache('myCache', 86400, null);
        if($cache->get('myCache', 0) == null) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('BookBundle:Book');
            $query = $repository->createQueryBuilder('book')
                ->orderBy('book.dateRead', 'ASC')
                ->getQuery();
            $books = $query->useResultCache(true,86400,'myCache')->getArrayResult();
            $cache->set('myCache',$books,86400);
        } else {
            $books = $cache->get('myCache');
        }
        $response = new Response(json_encode($books));
        $response->headers->set('Content-Type', 'application/json');
        $response->setSharedMaxAge(86400);

        return $response;
    }

    /**
     * Creates a add book entity.
     *
     * api
     *
     * @Route("/added", name="book_added")
     * @Method({"GET", "POST"})
     */
    public function addedAction(Request $request, Book $book)
    {
        //в createBuilder-e не нашел метод insert, поэтому через persist и flush
        $cache = new FilesystemCache('myCache', 86400, null);
        $cache->clear();
        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();
        return $book;
    }

    /**
     *
     * Edit book
     *
     * api
     *
     * @Route("/edit/{id}", name="edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function myEditAction(Book $book)
    {
        $cache = new FilesystemCache('myCache', 86400, null);
        $cache->clear();
        $repository = $this->getDoctrine()->getManager()->getRepository('BookBundle:Book');
        $query = $repository->createQueryBuilder('b')
            ->where('b.id=:id')
            ->setParameter('id',$book->getId())
            ->getQuery();
        $arr = $query->getSingleResult();//вернет сущность Book которую нужно править

        $book = $arr;

        //пробывал сделать новый экземпляр класса с путями уже имеющегося файлов, но editform-a пустая
        //$b = new File($arr->getBook());
        //$t = new File($arr->getTitle());
        //$book->setBook($b);
        //$book->setTitle($t);
        return $book;
    }
}