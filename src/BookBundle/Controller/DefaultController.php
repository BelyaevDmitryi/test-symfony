<?php

namespace BookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function booksAction()
    {
        return $this->render('BookBundle:Default:index.html.twig');
    }
}
