<?php

namespace BookBundle\Controller;

use BookBundle\Entity\Book;
use BookBundle\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\Request;

/**
 * Book controller.
 *
 * @Route("book")
 */
class BookController extends Controller
{
    private $cache;
    /**
     * Lists all book entities.
     *
     * api/v1/books
     *
     * @Route("/", name="book_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        //выбрали все из доктрины
        $books = $em->getRepository('BookBundle:Book')->findAll();

        $this->cache = new FilesystemCache('myCache', 86400);
        if (!$this->cache->has('myCache')){
            $this->cache = new FilesystemCache('myCache', 86400);
        } else {
            $this->cache->get('myCache');
        }
        //сортируем по времени
        //передаем в функцию сортировки
        usort($books, function ($a, $b) {
            if($a->getDateRead()->format("Y-m-d H:i:s") < $b->getDateRead()->format("Y-m-d H:i:s")) return -1;
            elseif($a->getDateRead()->format("Y-m-d H:i:s") > $b->getDateRead()->format("Y-m-d H:i:s")) return 1;
            else return 0;
        });
        //рендерим
        return $this->render('BookBundle:book:index.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Creates a add book entity.
     *
     * api/v1/books/add
     *
     * @Route("/add", name="book_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        $tmp = new FilesystemCache();
        $tmp->get('myCache');
        $this->cache = $tmp;
        if ($this->cache->has('myCache')){
            $this->cache->get('myCache');
            $this->cache->clear();
        }

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('book_index', array('id' => $book->getId()));
        }

        return $this->render('@Book/book/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * api/v1/books/{id}/edit
     *
     * @Route("/{id}/edit", name="book_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);
        $editForm = $this->createForm(BookType::class, $book);
        $editForm->handleRequest($request);

        $tmp = new FilesystemCache();
        $tmp->get('myCache');
        $this->cache = $tmp;
        if ($this->cache->has('myCache')){
            $this->cache->get('myCache');
            $this->cache->clear();
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('book_index', array('id' => $book->getId()));
        }

        return $this->render('BookBundle:book:edit.html.twig', array(
            'book' => $book,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a book entity.
     *
     * @Route("/{id}", name="book_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Book $book)
    {
        $form = $this->createDeleteForm($book);
        $form->handleRequest($request);

        $tmp = new FilesystemCache();
        $tmp->get('myCache');
        $this->cache = $tmp;
        if ($this->cache->has('myCache')){
            $this->cache->get('myCache');
            $this->cache->clear();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            unlink($book->getTitle());
            unlink($book->getBook());
            $em->remove($book);
            $em->flush();
        }

        return $this->redirectToRoute('book_index');
    }

    /**
 * Creates a form to delete a book entity.
 *
 * @param Book $book The book entity
 *
 * @return \Symfony\Component\Form\Form The form
 */
    private function createDeleteForm(Book $book)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('book_delete', array('id' => $book->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * Displays a form to document an existing book entity.
     *
     * @Route("uploads/books/{id}", name="book_document", requirements={"id": "\s+"})
     * @Method("GET")
     */
    public function documentAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('BookBundle:Book')->find($id);

        return $this->redirectToRoute('book_document', array('id' => $book->getBook()));
    }

    /**
     * Displays a form to titles an existing book entity.
     *
     * @Route("/uploads/titles/{id}", name="book_image", requirements={"id": "\s+"})
     * @Method("GET")
     */
    public function imageAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('BookBundle:Book')->find($id);

        return $this->redirectToRoute('book_image', array('id' => $book->getTitle()));
    }

    /**
     * Download books files
     *
     * @Route("/downloads/books/{id}", name="download_books")
     * @Method("GET")
     */
    public function downloadBookAction(Book $book)
    {
        $fileType = $book->getBookOrig();
        $file = $book->getBook();
        header("Content-Length: " . filesize($file));
        header("Content-Disposition: attachment; filename=".$fileType);
        readfile($file);
        return $this->redirectToRoute('book_index', array('book' => $book->getBook()));
    }

    /**
     * Download titles files
     *
     * @Route("/downloads/titles/{id}", name="download_titles")
     * @Method("GET")
     */
    public function downloadTitleAction(Book $book)
    {
        $fileType = $book->getTitleOrig();
        $file = $book->getTitle();
        header("Content-Length: " . filesize($file));
        header("Content-Disposition: attachment; filename=".$fileType);
        readfile($file);
        return $this->redirectToRoute('book_index', array('title' => $book->getTitle()));
    }
}
