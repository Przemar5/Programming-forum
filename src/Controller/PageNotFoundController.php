<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageNotFoundController extends AbstractController
{
    public function index()
    {
        return $this->render('error/page_not_found.html.twig');
    }
}
