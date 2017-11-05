<?php

namespace BookBundle\Controller;

use BookBundle\Entity\Book;
use BookBundle\Form\BookType;;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Book controller.
 *
 * @Route("book")
 */
class BookController extends ApiController
{

    /**
     * @Route("/", name="book_homepage")
     * @Method("GET")
     *
     */
    public function mainAction()
    {
        return $this->render(':default:index.html.twig');
    }

    /**
     * Lists all book entities.
     *
     * books
     *
     * @Route("/", name="book_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        //получаем данные в формате json
        $books = $this->selectAllAction()->getContent();
        //decode
        $books = json_decode($books,true);
        //рендерим

        $response = $this->render('BookBundle:book:index.html.twig', array(
            'books' => $books,
        ));
        $response->setSharedMaxAge(86400);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        return $response;
    }

    /**
     * Creates a add book entity.
     *
     * books/add
     *
     * @Route("/add", name="book_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $this->addedAction($request, $book);

            return $this->redirectToRoute('book_index');
        }

        return $this->render('@Book/book/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * books/{id}/edit
     *
     * @Route("/{id}/edit", name="book_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);
        //в $request добавить файлы
//        $b = new UploadedFile($book->getTitle(),$book->getTitleOrig());
//        $t = new UploadedFile($book->getBook(),$book->getBookOrig());
//        $request->files->add(array($t,$b));

        $editForm = $this->createForm(BookType::class, $book);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $book = $this->myEditAction($book);
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
