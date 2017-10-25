<?php

namespace BookBundle\Controller;

use BookBundle\Entity\Book;
use BookBundle\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * Book controller.
 *
 * @Route("book")
 */
class BookController extends Controller
{
    /**
     * Lists all book entities.
     *
     * @Route("/", name="book_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('BookBundle:Book')->findAll();

        return $this->render('BookBundle:book:index.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/new", name="book_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cache = new FilesystemCache();
        $cache->clear();
        $cache->set('myCache', 4711, 86400);

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            $book->setCache($cache);

            return $this->redirectToRoute('book_show', array('id' => $book->getId()));
        }

        return $this->render('BookBundle:book:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a book entity.
     *
     * @Route("/{id}/show", name="book_show")
     * @Method("GET")
     */
    public function showAction(Book $book)
    {
        $cache = new FilesystemCache();
        if (!$cache->has('myCache')){
            $cache->set('myCache', 4711, 86400);
            $book->setCache($cache);
        } else {
            $book->setCache($cache->get('myCache'));
        }

        $deleteForm = $this->createDeleteForm($book);

        return $this->render('BookBundle:book:show.html.twig', array(
            'book' => $book,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * @Route("/{id}/edit", name="book_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);
        $editForm = $this->createForm(BookType::class, $book);
        $editForm->handleRequest($request);

        $cache = new FilesystemCache();
        $cache->clear();
        $cache->set('myCache', 4711, 86400);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $book->setCache($cache);

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

        $cache = new FilesystemCache();
        $cache->clear();
        $cache->set('myCache', 4711, 86400);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($book);
            $em->flush();
        }

        $book->setCache($cache);

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
     * @Route("/{book}", name="book_document", requirements={"title": "\s+"})
     * @Method("GET")
     */
    public function documentAction(Book $book)
    {
        return $this->redirectToRoute('book_document', array('book' => $book->getBook()));
    }

    /**
     * Displays a form to titles an existing book entity.
     *
     * @Route("/{title}", name="book_image", requirements={"title": "\s+"})
     * @Method("GET")
     */
    public function imageAction(Book $book)
    {
        return $this->redirectToRoute('book_image', array('title' => $book->getTitle()));
    }
}
